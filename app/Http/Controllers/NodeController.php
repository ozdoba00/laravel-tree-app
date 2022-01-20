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
        $node = new Node();

        if(sizeof($nodes)> 0){

            return $node->buildTree($nodes, 0, false);
    }else{
        return ["message"=> "There is no any nodes"];
    }

    }

    public function indexClicked(){
        $nodes = Node::all();
        $node = new Node();

        if(sizeof($nodes)> 0){

            return ['tree'=>$node->buildTree($nodes, 0, true), 'list'=>$nodes];
    }else{
        return ["message"=> "There is no any nodes"];
    }
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
        $node->is_node = $request->is_node;

        $children = $node->find_children($nodes, $request->id);
        foreach ($children as  $value) {
            if($value['parent_id']==$request->id){
            if($request->name==$value['name'] && $request->is_node==$value['is_node']){
                return ['message'=> "There is a node with that name!"];
            }}

        }


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
        $node = new Node();
        $nodes = Node::all();
        $nodesArr = [];
        $parentId = Node::find($id)['parent_id'];
        foreach ($nodes as $item){

        if($item['id']!=$id && $item['id']!=$parentId && $item['is_node'])
            array_push($nodesArr, $item);
        }


        $children = $node->find_children($nodes, $id);

        $subArr = array_diff($nodesArr, $children);


        return $subArr;

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
        $nodes = Node::all();

        $node->name = $request->name;
        $node->is_node = $node->is_node;

        $children = $node->find_children($nodes, $node->parent_id);

        foreach ($children as  $value) {
            if($value['parent_id']==$node->parent_id){
            if($node->name==$value['name'] && $node->is_node==$value['is_node']){
                return ['message'=> "There is a node with that name!"];
            }}

        }
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

    public function move(Request $request, $id){

        $node = Node::find($id);
        $nodes = Node::all();
        $node->parent_id = $request->id;

        $children = $node->find_children($nodes, $request->id);

        foreach ($children as  $value) {
            if($value['parent_id']==$node->parent_id){
            if($node->name==$value['name'] && $node->is_node==$value['is_node']){
                return ['message'=> "There is a node with that name!"];
            }}

        }

        $node->save();
    }
}
