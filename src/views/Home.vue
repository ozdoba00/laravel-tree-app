<template>
  <div class="home">
    
    <div class="container">
      <div class="row">
       <div class="col-10">
         <button v-bind:id="0" v-on:click="select($event)">Add main folder</button>
         <Tree v-on:click.native="select($event)" :items="items"/>

        
    </div>
    
    </div>
    <!-- <button v-on:click="test">Click</button> -->
  </div>
  </div>
</template>

<script>
// @ is an alias to /src

import axios from 'axios';
import Tree from "../components/Tree.vue";

export default {
  name: 'Home',
  
  data(){
    return{
      items: [],
      nodeArr: [],
      clickedItems: []
    }
  },
  methods: {

    test: function(){
      
      this.getData();
    },
    
    setClickedElement: function(ids){

     
      const idMap = this.nodeArr.reduce(function merge(map, node) {
        map[node.id] = node;
        
        if (Array.isArray(node.children)) {
          node.children.reduce(merge, map);
        }
        
        return map;
      }, {});

      const items = ids.map(id => idMap[id]);
      
      items.forEach(item => {
        
        if(item.children){
          if(!item.clicked){
            item.clicked = true;
            this.clickedItems.push(item.id);
          }
          else{
            item.clicked= false;
            let index = this.clickedItems.indexOf(item.id);
            if (index !== -1) {
              this.clickedItems.splice(index, 1);
            }
          }
        }
      });
 
 
    localStorage.setItem("clickedItems", this.clickedItems);
    this.items = this.nodeArr;
    },
    select: function(event){
      
      
      if(event.target.id && (event.target.tagName != "BUTTON"))
        this.setClickedElement([event.target.id]);
      else if(event.target.id && !event.target.className && event.target.tagName == "BUTTON"){
       this.addNewNode(event.target.id);
      }
      else if(event.target.id && event.target.className == "delete"){
        this.removeNode(event.target.id);
      }
   
    },
    getData: function(){
      
      axios.get(`http://127.0.0.1:8001/api/node/get`)
    .then(response => {
      console.log(response.data);
      if(!response.data.message){
      this.nodeArr = response.data;

      
      this.items = this.nodeArr;
      
      this.clickedItems = [];
      if(localStorage.getItem('clickedItems')){
        this.setClickedElement(localStorage.getItem("clickedItems").split(","));
      }
      }else{
        this.items = [];
        localStorage.setItem("clickedItems", '');
      }
      
    })
    },

    addNewNode: function(nodeId){
      let nodeName = prompt("Wpisz nazwe wezla", "Folder");
      if(nodeId == "0")
        nodeId = null;
      axios.post('http://127.0.0.1:8001/api/node/add', {
        name: nodeName,
        id: nodeId
      })
                .then(function( response ){
                  console.log(response);
                    this.getData();
                }.bind(this));
      

    },

    removeNode: function(nodeId){
      
      axios.post('http://127.0.0.1:8001/api/node/remove/'+nodeId, {
        _method: "delete"
      })
                .then(function( response ){
                  console.log(response);
                    this.getData();
                }.bind(this));
    }
    
    },
  components: {
    Tree
  },
   created() {
    
    this.getData();
  
   
  }
}



</script>


<style >
 div .row{
   display:flex;
   justify-content: left;
 }
</style>