<?php
declare(strict_types=1);
namespace GridCP\Net\Common\Application\Helpers;
use GridCP\Common\Domain\Const\Ip4\Constants;


use IPCalc\IP;

trait CalcIps
{
    public function getIp4s(IP $ipCal):array
    {
        $result = [];
        if (!$ipCal->getIp()) {
            return $result;
        }

        $ipInit = long2ip(ip2long($ipCal->getIp()) & ip2long($ipCal->getNetmask()));
        $ipEnd = long2ip(ip2long($ipInit) | (~ip2long($ipCal->getNetmask())));
    

        $ipCurrent = ip2long($ipInit);
        $ipEndLong = ip2long($ipEnd);
    

        while ($ipCurrent <= $ipEndLong) {
            $result[] = long2ip($ipCurrent);
            $ipCurrent++;
        }
    
        return $result;
    }

    public function nextIP(&$ip) {
        $bytes = explode('.', $ip);
        $bytes[Constants::IP_BYTE_POSITION]++;
        
        for ($i = Constants::IP_BYTE_POSITION; $i >= 0; $i--) {
            if ($bytes[$i] > Constants::MAX_BYTE_VALUE) {
                $bytes[$i] = 0;
                if ($i > 0) {
                    $bytes[$i - 1]++;
                }
            } else {
                break;
            }
        }
        $ip = implode('.', $bytes);
    }


    public function findDuplicateIps(IP $inputIp, array $existingSubnets): array
    {
        $expandedIps = [];
        $duplicates = [];


        foreach ($existingSubnets as $subnet) {
            if ($this->isNotNullIp($subnet)) {
                $ipCalc = new IP($subnet);
                $expandedIps = array_merge($expandedIps, $this->getIp4s($ipCalc));
            }
        }


        $inputIps = $this->getIp4s($inputIp);
        foreach ($inputIps as $ip) {
            if (in_array($ip, $expandedIps, true)) {
                $duplicates[] = $ip;
            }
        }

        return $duplicates;
    }

    private function isNotNullIp($ip): bool
    {
        $pattern = '/^(\d{1,3}\.){3}\d{1,3}(\/\d{1,2})?$/';
        return !empty($ip) && preg_match($pattern, $ip);
    }


    public function findAvailableSubnets($ipList, $subnetPrefix) {
        $ipIntegers = $this->convertIpsToIntegers($ipList);
        $blocks = $this->groupIpsIntoBlocks($ipIntegers);
        return $this->processBlocksForAvailableSubnets($blocks, $subnetPrefix);
    }

    private function convertIpsToIntegers($ipList) {
        $ipIntegers = array_unique(array_map('ip2long', $ipList));
        sort($ipIntegers);
        return $ipIntegers;
    }

    private function groupIpsIntoBlocks($ipIntegers) {
        $blocks = [];
        $currentBlock = [$ipIntegers[0]];

        for ($i = 1; $i < count($ipIntegers); $i++) {
            if ($ipIntegers[$i] - end($currentBlock) > 1) {
                $blocks[] = $currentBlock;
                $currentBlock = [];
            }
            $currentBlock[] = $ipIntegers[$i];
        }
        $blocks[] = $currentBlock;

        return $blocks;
    }

    private function processBlocksForAvailableSubnets($blocks, $subnetPrefix) {
        $availableSubnets = [];
        $subnetSize = 1 << (32 - $subnetPrefix);

        foreach ($blocks as $block) {
            $blockMin = $block[0];
            $blockMax = $block[count($block) - 1];
            $networkStart = $blockMin - ($blockMin % $subnetSize);

            for ($subnetStart = $networkStart; $subnetStart <= $blockMax; $subnetStart += $subnetSize) {
                if ($this->allIpsInSubnet($block, $subnetStart, $subnetSize)) {
                    $availableSubnets[] = long2ip($subnetStart);
                }
            }
        }

        return $availableSubnets;
    }

    private function allIpsInSubnet($block, $subnetStart, $subnetSize) {
        $subnetEnd = $subnetStart + $subnetSize - 1;

        for ($ip = $subnetStart; $ip <= $subnetEnd; $ip++) {
            if (!in_array($ip, $block)) {
                return false;
            }
        }

        return true;
    }

    public function getSubnetsFromIpList(array $ipList, int $mask): array
    {
        $result = [];
        $subnetSize = 1 << (32 - $mask);
        $ipIntegers = array_unique(array_map('ip2long', $ipList));
        sort($ipIntegers);
    
        foreach ($ipIntegers as $ip) {
            $subnetStart = $ip - ($ip % $subnetSize);
            $subnetEnd = $subnetStart + $subnetSize - 1;
    
            $subnet = long2ip($subnetStart) . "/$mask";
    
            $subnetIps = [];
            for ($currentIp = $subnetStart; $currentIp <= $subnetEnd; $currentIp++) {
                if (in_array($currentIp, $ipIntegers)) {
                    $subnetIps[] = long2ip($currentIp);
                }
            }
    
            if (!empty($subnetIps) && !isset($result[$subnet])) {
                $result[$subnet] = $subnetIps;
            }
        }
    
        return $result;
    }

    public function getSubnetsFromIps(array $ipList, int $mask): array
    {
        $result = [];
        $subnetSize = 1 << (32 - $mask);
        $ipIntegers = $this->convertIpsToIntegerList($ipList);
        $usedIps = [];
    
        foreach ($ipIntegers as $ip) {
            if (in_array($ip, $usedIps)) {
                continue;
            }
    
            $subnetStart = $ip - ($ip % $subnetSize);
            $subnetIps = range($subnetStart, $subnetStart + $subnetSize - 1);
    
            if ($this->areAllIpsInSubnet($ipIntegers, $subnetIps)) {
                $result[] = long2ip($subnetStart);
                $usedIps = array_merge($usedIps, $subnetIps);
            }
        }
    
        return $result;
    }
    
    private function convertIpsToIntegerList(array $ipList): array
    {
        $ipIntegers = array_unique(array_map('ip2long', $ipList));
        sort($ipIntegers);
        return $ipIntegers;
    }
    
    private function areAllIpsInSubnet(array $ipIntegers, array $subnetIps): bool
    {
        foreach ($subnetIps as $ip) {
            if (!in_array($ip, $ipIntegers, true)) {
                return false;
            }
        }
        return true;
    }

}