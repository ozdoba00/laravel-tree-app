<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreNode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\Node;

class NodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nodes = Node::all();

        if(sizeof($nodes)> 0){
        $children = [];



        foreach ($nodes as &$item) {
        $item->clicked = false;
        $children[$item['parent_id']][] = &$item;
        unset($item);
        }

        foreach ($nodes as &$item) if (isset($children[$item['id']]))
            $item['children'] = $children[$item['id']];

        return array_values($children)[0];
    }else{
        return ["message"=> "There is no any nodes"];
    }



        // return typeOf($nodes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNode $request)
    {
        $node = new Node();

        $nodes = Node::all();

        $node->name = $request->name;
        $node->is_node = true;

        // Warunek jezeli dodawany wezel jest jako pierwszy w bazie
        if(!empty($nodes) && (!$request->id)){
            $node->parent_id = null;
            $node->save();
        }

        // Sprawdzenie czy id jest podane oraz czy taki wezel o danym id istnieje
        if($request->id && (Node::where('id', $request->id)->exists()) && Node::find($request->id)->is_node ){
            $node->parent_id = $request->id;
            $node->save();
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $node = Node::find($id);

        return $node;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreNode $request, $id)
    {
        $node = Node::find($id);


        $node->name = $request->name;
        $node->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Node::destroy($id);
    }

    public function move($id){

    }
}
