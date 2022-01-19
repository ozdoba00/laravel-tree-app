<template>
  <div class="home">
    
    <div class="container">
      <div class="row">
       <div class="col-12">
         <button class="addFolder" v-bind:id="0" v-on:click="select($event)">Add main folder</button>
         <button class="addFile" v-bind:id="0" v-on:click="select($event)">Add File</button>
         <br>
         <label for="folderName">Zaznaczony folder</label>
         <input type="text" name="folderName" readonly v-model="message">
         <input type="hidden" name="folderId" v-model="selectedNodeId">
         <br>
         <select name="nodeList" v-model="selectedOption" v-if="message">
           <option v-for="item of possibleMoves" :key="item.id" v-bind:value="item.id">{{item.name}}</option>
         </select>
         <button  v-if="message" v-on:click="moveNode($event)">Przenieś</button>
         <Tree v-on:click.native="select($event)" :items="items"/>

        
    </div>
    
    </div>
    
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
      clickedItems: [],
      message: "",
      selectedNodeId: "",
      possibleMoves: [],
      selectedOption: []
    }
  },
  methods: {

    moveNode: function(event){
      console.log(event);
      console.log(this.selectedOption);
      
      axios.post('http://127.0.0.1:8001/api/node/move/'+this.selectedNodeId, {
        _method: "put",
        id: this.selectedOption
      })
          .then(function( response ){
            console.log(response);
              this.getData();
          }.bind(this));
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
      
      
      if(event.target.id && (event.target.tagName != "BUTTON")){
        this.message = event.target.textContent;
        this.selectedNodeId = event.target.id;
        this.getDataToMove(this.selectedNodeId);
        this.setClickedElement([event.target.id]);
      }
      else if(event.target.id && event.target.className == "addFolder" && event.target.tagName == "BUTTON"){
       this.addNewNode(event.target.id, true);
      }
      else if(event.target.id && event.target.className == "delete"){
        this.removeNode(event.target.id);
      }else if(event.target.id && event.target.className == "addFile"){
        this.addNewNode(event.target.id, false);
      }
   
    },

    getDataToMove: function(id){
      axios.get(`http://127.0.0.1:8001/api/node/get/`+id)
    .then(response => {
      
      this.possibleMoves = response.data;
      
    })
    },
    getData: function(){
      
      axios.get(`http://127.0.0.1:8001/api/node/get`)
    .then(response => {
      // console.log(response.data);
      if(!response.data.message){
        
        console.log(response.data);
      this.nodeArr = response.data;

      
      this.items = this.nodeArr;
      
      // console.log(this.nodeArr);
     
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

    addNewNode: function(nodeId, isNode){
      let nodeName = prompt("Wpisz nazwe wezla", "Folder");
      if(nodeId == "0")
        nodeId = null;
      axios.post('http://127.0.0.1:8001/api/node/add', {
        name: nodeName,
        id: nodeId,
        is_node: isNode
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
 button > * {
  pointer-events: none;
}
</style>