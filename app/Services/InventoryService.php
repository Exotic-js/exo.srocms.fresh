<?php

namespace App\Services;

use App\Models\SRO\Account\ItemNameDesc;
use App\Models\SRO\Shard\BindingOptionWithItem;
use App\Models\SRO\Shard\Chest;
use App\Models\SRO\Shard\InvCOS;
use App\Models\SRO\Shard\Inventory;
use App\Models\SRO\Shard\InventoryForAvatar;
use App\Models\SRO\Shard\TradeEquipInventory;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Get inventory items for a character within a slot range.
     *
     * @param int $CharID
     * @return array
     */
    public function getInventorySet(int $CharID, $max, $min, $not): array
    {
        $inventory = Inventory::getInventory($CharID, $max, $min, $not);
        return $this->convertItemList($inventory);
    }

    /**
     * Get avatar inventory for a character.
     *
     * @param int $CharID
     * @return array
     */
    public function getInventoryAvatar(int $CharID): array
    {
        $inventory = InventoryForAvatar::getInventoryForAvatar($CharID);
        return $this->convertItemList($inventory);
    }

    /**
     * Get job inventory for a character.
     *
     * @param int $CharID
     * @return array
     */
    public function getInventoryJob(int $CharID): array
    {
        $inventory = TradeEquipInventory::getInventoryForJob($CharID);
        return $this->convertItemList($inventory);
    }

    /**
     * Get storage items for a character within a slot range.
     *
     * @param int $UserJID
     * @return array
     */
    public function getStorageItems(int $UserJID, $max, $min): array
    {
        $inventory = Chest::getChest($UserJID, $max, $min);
        return $this->convertItemList($inventory);
    }

    /**
     * Get Pet items for a character within a slot range.
     *
     * @param int $CharID
     * @return array
     */
    public function getPetItems(int $CharID, $PetID, $max, $min): array
    {
        $inventory = InvCOS::getPetItems($CharID, $PetID, $max, $min);
        return $this->convertItemList($inventory);
    }

    /**
     * Convert raw inventory data into structured format.
     *
     * @param array|null $inventory
     * @return array
     */
    public function convertItemList(?array $inventory): array
    {
        if (!$inventory) {
            return [];
        }

        $convertedItems = [];

        foreach ($inventory as $item) {
            try {
                $convertedItems[] = $this->processItem($item);
            } catch (\Throwable $e) {
                Log::error('Error processing inventory item', [
                    'item' => $item,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $convertedItems;
    }

    /**
     * Process a single inventory item.
     *
     * @param array $item
     * @return array
     */
    private function processItem(array $item): array
    {
        return [
            'Slot' => $item['Slot'] ?? $item['ID64'],
            'Amount' => $item['MaxStack'] > 1 ? $item['Data'] : 0,
            'ImgPath' => $this->getItemIcon($item['AssocFileIcon128']),
            'ItemInfo' => $this->getItemInfo($item),
        ];
    }

    /**
     * Get item name from configuration or fallback.
     *
     * @param array $item
     * @return string
     */
    private function getItemName(array $item): string
    {
        return ItemNameDesc::getItemRealName($item['NameStrID128'] ?? 'Unknown');
    }

    /**
     * Get item icon path.
     *
     * @param string $assocFile
     * @return string
     */
    private function getItemIcon(?string $assocFile): string
    {
        $iconPath = str_replace('\\', '/', trim($assocFile));
        $iconPath = preg_replace('/\.ddj$/i', '', $iconPath);
        $iconPath = 'images/sro/' . $iconPath . '.png';
        $iconPath = strtolower($iconPath);

        if (!file_exists(public_path($iconPath))) {
            return 'images/sro/icon_default.png';
        }

        return $iconPath;
    }

    /**
     * Get detailed item information.
     *
     * @param array $item
     * @return array
     */
    private function getItemInfo(array $item): array
    {
        $info = [
            'ID64' => $item['ID64'],
            'RefItemID' => $item['RefItemID'],
            'Serial64' => $item['Serial64'],
            'CodeName128' => $item['CodeName128'],
            'ReqLevelType1' => $item['ReqLevelType1'],
            'ReqLevelType2' => $item['ReqLevelType2'],
            'ReqLevelType3' => $item['ReqLevelType3'],
            'ReqLevelType4' => $item['ReqLevelType4'],
            'ReqLevel1' => $item['ReqLevel1'],
            'ReqLevel2' => $item['ReqLevel2'],
            'ReqLevel3' => $item['ReqLevel3'],
            'ReqLevel4' => $item['ReqLevel4'],
            'ItemClass' => $item['ItemClass'],
            'MagParamNum' => $item['MagParamNum'],
            'MagParam1' => $item['MagParam1'],
            'MaxMagicOptCount' => $item['MaxMagicOptCount'],
            'ChildItemCount' => $item['ChildItemCount'],
            'Slot' => $item['Slot'],
            'Data' => $item['Data'],
            'TypeID1' => $item['TypeID1'],
            'TypeID2' => $item['TypeID2'],
            'TypeID3' => $item['TypeID3'],
            'TypeID4' => $item['TypeID4'],
        ];

        $info['ItemName'] = $this->getItemName($item);
        $info['ItemDesc'] = config('itemdesc')[$item['DescStrID128']] ?? null;
        $info['Amount'] = $item['MaxStack'] > 1 ? $item['Data'] : 0;
        $info['OptLevel'] = $item['OptLevel'] ?? 0;
        $info['nOptValue'] = $item['nOptValue'] ?? 0;
        $info['Country'] = $item['Country'] == 0 ? 'Chinese' : 'European';
        $info['Gender'] = $item['ReqGender'] == 0 ? 'Female' : 'Male';
        $info['SoxType'] = $this->getSoxType($item);
        $info['SoxName'] = $this->getSoxName($item);
        $info['Degree'] = (int)ceil($item['ItemClass'] / 3) ?? null;
        $info['JobDegree'] = config('item.job_degree')[$item['ItemClass']] ?? null;
        $info['Type'] = config('item.types')[$item['TypeID1']][$item['TypeID2']][$item['TypeID3']][$item['TypeID4']] ?? null;
        $info['Detail'] = config('item.detail')[$item['Slot']] ?? null;
        $info['DevilMaxHP'] = $this->getDevilMaxHP($item);
        $info['WhiteInfo'] = $this->getWhiteInfo($item);
        $info['BlueInfo'] = $this->getBlueInfo($item);
        $info['TimeEnd'] = $this->getTimeEnd($item);

        return $info;
    }

    private function getSoxType($item): ?string
    {
        $config = config('item.sox_type');
        foreach ($config as $itemClass => $CodeName) {
            if ($item['ItemClass'] > $itemClass) {
                foreach ($CodeName as $key => $value) {
                    if (str_contains($item['CodeName128'], $key)) {
                        return $value;
                    }
                }
            }
        }

        return 'Normal';
    }

    private function getSoxName($item): ?string
    {
        $config = config('item.sox_name');
        foreach ($config as $key => $values) {
            if (str_contains($item['CodeName128'], $key)) {
                return $values[$item['Slot']] ?? '';
            }
        }

        return '';
    }


    private function getDevilMaxHP($item): ?int
    {
        $config = config('item.devil_type');
        uksort($config, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        foreach ($config as $key => $value) {
            if (str_contains($item['CodeName128'], $key)) {
                return $value;
            }
        }

        return 0;
    }

    public function getTimeEnd(array $item): string
    {
        if ($item['Data'] === 0) {
            return '28Day';
        }

        if (time() > $item['Data']) {
            return 'Awaken period is over';
        }

        $difference = $item['Data'] - time();
        $days = intdiv($difference, 3600 * 24);
        $difference %= 3600 * 24;
        $hours = intdiv($difference, 3600);
        $difference %= 3600;
        $minutes = intdiv($difference, 60);
        $seconds = $difference % 60;

        return sprintf('%dDay %02dHour %02dMinute', $days, $hours, $minutes);
    }

    private function getBlueInfo($item): array
    {
        $config = config('magopt');
        $blueInfo = [];

        $exclude = [
            'MATTR_PET_RESIST_FEAR',
            'MATTR_PET_RESIST_SLEEP',
        ];

        $bits = [512, 64, 8, 1];
        $param1 = (int) (($item['MagParam1'] ?? 0) > 4611686018427387904 ? $item['MagParam1'] - 4611686018427387904 : ($item['MagParam1'] ?? 0));

        if (config('global.server.version') !== 'vSRO') {
            foreach ($bits as $bit) {
                $count = intdiv($param1, $bit);
                if ($count > 0) {
                    $param1 -= $count * $bit;
                    foreach ($config as $id => $opt) {
                        if (
                            ($bit === 512 && $opt['name'] === 'MATTR_ASTRAL') ||
                            ($bit === 64 && $opt['name'] === 'MATTR_LUCK') ||
                            ($bit === 8 && $opt['name'] === 'MATTR_SOLID') ||
                            ($bit === 1 && $opt['name'] === 'MATTR_ATHANASIA')
                        ) {
                            $blueInfo[] = [
                                'id' => $id,
                                'code' => $opt['name'],
                                'name' => str_replace('%desc%', $count, $opt['desc']),
                                'value' => $count,
                                'mLevel' => $opt['mLevel'],
                                'mValue' => 0,
                                'sortkey' => $opt['sortkey'],
                            ];
                            break;
                        }
                    }
                }
            }
        }

        for ($i = 1; $i <= ($item['MagParamNum'] ?? 12); $i++) {
            if (!isset($item["MagParam{$i}"]) || $item["MagParam{$i}"] <= 1) {
                continue;
            }

            if ($item["MagParam{$i}"] === 65) {
                $blueInfo[] = [
                    'id' => 0,
                    'code' => 'MATTR_DUR',
                    'name' => 'Repair invalid (Maximum durability 400% increase)',
                    'value' => 400,
                    'mLevel' => 0,
                    'mValue' =>  0,
                    'sortkey' => 0
                ];
                continue;
            }

            $hexParam = str_pad(dechex($item["MagParam{$i}"]), 11, '0', STR_PAD_LEFT);
            $id = hexdec(substr($hexParam, 3));
            $value = hexdec(substr($hexParam, 0, 3));

            if (!isset($config[$id])) {
                continue;
            }

            if (in_array($config[$id]['name'], $exclude, true)) {
                continue;
            }

            if ($config[$id]['name'] === 'MATTR_REPAIR') {
                $value--;
            }

            $blueInfo[] = [
                'id' => $id,
                'code' => $config[$id]['name'],
                'name' => str_replace('%desc%', $value, $config[$id]['desc']),
                'value' => $value,
                'mLevel' => $config[$id]['mLevel'],
                'mValue' => $config[$id]['mValue'] ?? 0,
                'sortkey' => $config[$id]['sortkey'],
            ];
        }

        usort($blueInfo, fn($a, $b) => $a['sortkey'] <=> $b['sortkey']);
        return $blueInfo;
    }

    private function getWhiteInfo($item): array
    {
        $OptLevel = $item['OptLevel'] ?? 0;
        $Variance = $item['Variance'] ?? 0;

        $percentage = function ($variance, $index) {
            return (int) floor(((int) ($variance / pow(32, $index)) & 0x1F) * 3.23);
        };

        return [
            'PAtack' => ($item['PAttackMin_L'] > 0 && $item['PAttackMax_L'] > 0)
                ? sprintf(
                    'Phy. atk. pwr. %d ~ %d (+%d%%)',
                    round(($item['PAttackMin_L'] + $item['PAttackInc'] * $OptLevel) + (($item['PAttackMin_U'] - $item['PAttackMin_L']) * $percentage($Variance, 4) / 100)),
                    round(($item['PAttackMax_L'] + $item['PAttackInc'] * $OptLevel) + (($item['PAttackMax_U'] - $item['PAttackMax_L']) * $percentage($Variance, 4) / 100)),
                    $percentage($Variance, 4)
                )
                : '',
            'MAtack' => ($item['MAttackMin_L'] > 0 && $item['MAttackMax_L'] > 0)
                ? sprintf(
                    'Mag. atk. pwr. %d ~ %d (+%d%%)',
                    (int)(($item['MAttackMin_L'] + $item['MAttackInc'] * $OptLevel) + (($item['MAttackMin_U'] - $item['MAttackMin_L']) * $percentage($Variance, 5) / 100)),
                    (int)(($item['MAttackMax_L'] + $item['MAttackInc'] * $OptLevel) + (($item['MAttackMax_U'] - $item['MAttackMax_L']) * $percentage($Variance, 5) / 100)),
                    $percentage($Variance, 5)
                )
                : '',
            'PDefance' => ($item['PD_L'] > 0)
                ? sprintf(
                    'Phy. def. pwr. %.1f (+%d%%)',
                    round(($item['PD_L'] + $item['PDInc'] * $OptLevel) + (($item['PD_U'] - $item['PD_L']) * $percentage($Variance, 3) / 100), 1),
                    $percentage($Variance, 3)
                )
                : '',
            'MDefance' => ($item['MD_L'] > 0)
                ? sprintf(
                    'Mag. def. pwr. %.1f (+%d%%)',
                    round(($item['MD_L'] + $item['MDInc'] * $OptLevel) + (($item['MD_U'] - $item['MD_L']) * $percentage($Variance, 4) / 100), 1),
                    $percentage($Variance, 4)
                )
                : '',
            'Durability' => ($item['Dur_U'] > 0)
                ? sprintf(
                    'Durability %d/%d (+%d%%)',
                    $item['Data'],
                    $item['Data'],
                    $percentage($Variance, 0)
                )
                : '',
            'BlockRate' => ($item['BR_L'] > 0)
                ? sprintf(
                    'Block Rate %d (+%d%%)',
                    (int)(($item['BR_L']) + (($item['BR_U'] - $item['BR_L']) * $percentage($Variance, 3) / 100)),
                    $percentage($Variance, 3)
                )
                : '',
            'AtackDist' => ($item['Range'] > 0)
                ? sprintf(
                    'Attack distance %.1f m',
                    $item['Range'] / 10
                )
                : '',
            'AtackRate' => ($item['HR_L'] > 0)
                ? sprintf(
                    'Attack rate %d (+%d%%)',
                    (int)(($item['HR_L'] + $item['HRInc'] * $OptLevel) + (($item['HR_U'] - $item['HR_L']) * $percentage($Variance, 3) / 100)),
                    $percentage($Variance, 3)
                )
                : '',
            'Critical' => ($item['CHR_L'] > 0)
                ? sprintf(
                    'Critical %d (+%d%%)',
                    (int)(($item['CHR_L']) + (($item['CHR_U'] - $item['CHR_L']) * $percentage($Variance, 6) / 100)),
                    $percentage($Variance, 6)
                )
                : '',
            'ParryRate' => ($item['ER_L'] > 0)
                ? sprintf(
                    'Parry rate %d (+%d%%)',
                    (int)(($item['ER_L'] + $item['ERInc'] * $OptLevel) + (($item['ER_U'] - $item['ER_L']) * $percentage($Variance, 5) / 100)),
                    $percentage($Variance, 5)
                )
                : '',
            'PReinforceWep' => ($item['PAStrMin_L'] > 0 && $item['PAStrMax_L'] > 0)
                ? sprintf(
                    'Phy. reinforce %.1f ~ %.1f (+%d%%)',
                    (float)(($item['PAStrMin_L']) + (($item['PAStrMin_U'] - $item['PAStrMin_L']) * $percentage($Variance, 1) / 100)) / 10,
                    (float)(($item['PAStrMax_L']) + (($item['PAStrMax_U'] - $item['PAStrMax_L']) * $percentage($Variance, 1) / 100)) / 10,
                    $percentage($Variance, 1)
                )
                : '',
            'MReinforceWep' => ($item['MAInt_Min_L'] > 0 && $item['MAInt_Max_L'] > 0)
                ? sprintf(
                    'Mag. reinforce %.1f ~ %.1f (+%d%%)',
                    (float)(($item['MAInt_Min_L']) + (($item['MAInt_Min_U'] - $item['MAInt_Min_L']) * $percentage($Variance, 2) / 100)) / 10,
                    (float)(($item['MAInt_Max_L']) + (($item['MAInt_Max_U'] - $item['MAInt_Max_L']) * $percentage($Variance, 2) / 100)) / 10,
                    $percentage($Variance, 2)
                )
                : '',
            'PReinforceSet' => ($item['PDStr_L'] > 0)
                ? sprintf(
                    'Phy. reinforce %.1f (+%d%%)',
                    (float)(($item['PDStr_L']) + (($item['PDStr_U'] - $item['PDStr_L']) * $percentage($Variance, 1) / 100)) / 10,
                    $percentage($Variance, 1)
                )
                : '',
            'MReinforceSet' => ($item['MDInt_L'] > 0)
                ? sprintf(
                    'Mag. reinforce %.1f (+%d%%)',
                    (float)(($item['MDInt_L']) + (($item['MDInt_U'] - $item['MDInt_L']) * $percentage($Variance, 2) / 100)) / 10,
                    $percentage($Variance, 2)
                )
                : '',
            'Pabsorp' => ($item['PAR_L'] > 0)
                ? sprintf(
                    'Phy. absorption %.1f (+%d%%)',
                    round(($item['PAR_L'] + $item['PARInc'] * $OptLevel) + (($item['PAR_U'] - $item['PAR_L']) * $percentage($Variance, 0) / 100), 1),
                    $percentage($Variance, 0)
                )
                : '',
            'Mabsorp' => ($item['MAR_L'] > 0)
                ? sprintf(
                    'Mag. absorption %.1f (+%d%%)',
                    round(($item['MAR_L'] + $item['MARInc'] * $OptLevel) + (($item['MAR_U'] - $item['MAR_L']) * $percentage($Variance, 1) / 100), 1),
                    $percentage($Variance, 1)
                )
                : '',
        ];
    }
}
