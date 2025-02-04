<?php
/**
 * vim:ft=php et ts=4 sts=4
 * @author Al Zee <z@alz.ee>
 * @version
 * @todo
 */

namespace App\Service;

use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;

class FieldSum
{
    public function calc(KeyValueStore $responseParameters, array $sumFieldNames): KeyValueStore
    {
        if (is_null($responseParameters->get('entities'))) {
            return $responseParameters;
        }

        $itr = $responseParameters->get('entities')->getIterator();
        foreach($sumFieldNames as $k => $v){
            $sum[$v] = 0;
        }
        $i = 0;
        while ($itr->valid()) {
            foreach($sumFieldNames as $k => $v){
                $getter = 'get' . ucfirst($v);
                $sum[$v] += $itr->current()->getInstance()->$getter();
            }
            // Is there a elegant way to get fields?
            if ($i == 0) {
                $fields = $itr->current()->getFields();
            }
            $i++;
            $itr->next();
        }

        if (isset($fields)) {
            foreach($sumFieldNames as $v){
                $fieldsum[$v] = $sum[$v] / 100;
            }
            $responseParameters->set('f', [
                'fieldsum' => $fieldsum ,
                'fields' => $fields
            ]);
        }

        return $responseParameters;
    }
}

