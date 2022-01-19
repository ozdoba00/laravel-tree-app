<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasFactory;

    public $timestamps = false;



    public function find_children($elements, $id) {

        $branch = array();


        foreach ($elements as $element) {
            $element->clicked = false;

            if($element['is_node']==1){
            if ($element['parent_id'] == $id ) {

                array_push($branch, $element);
                $children = $this->find_children($elements, $element['id']);


                foreach ($children as $value) {
                    array_push($branch, $value);
                }

            }
        }
        }
        return $branch;
        // return $branch;
    }



    public function buildTree($elements, $parentId = 0) {
        $branch = array();

        foreach ($elements as $element) {
            $element->clicked = false;
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
