<template>
  <div class="home">
    
    <div class="container">
      <div class="row">
       <div class="col-12">
         <div class="mainButtons">
         <button class="addFolder" v-bind:id="0" v-on:click="select($event)">Dodaj folder (pulpit)</button>
         <button class="addFile" v-bind:id="0" v-on:click="select($event)">Dodaj plik (pulpit)</button>
         <br>
          <button v-bind:id="0" v-on:click="expandNodes()">Rozwiń wszystko</button>
          <button v-bind:id="0" v-on:click="collapseNodes()">Zwiń wszystko</button>
         <br>
         </div>
         <label for="folderName">Zaznaczony folder</label>
         <input type="text" name="folderName" readonly v-model="message">
         <input type="hidden" name="folderId" v-model="selectedNodeId">
         <br>
         <select name="nodeList" v-model="selectedOption" v-if="message">
           <option v-for="item of possibleMoves" :key="item.id" v-bind:value="item.id">{{item.name}}</option>
         </select>
         <button  v-if="message" v-on:click="moveNode()">Przenieś</button>
         <Tree v-on:click.native="select($event)" :items="items"/>

        
    </div>
    
    </div>
    
  </div>
  </div>
</template>

<script>


import axios from 'axios';
import Tree from "../components/Tree.vue";

export default {
  name: 'Home',
  
  data(){
    return{
      items: [],
      nodeArr: [],
      nodesList: [],
      clickedItems: [],
      message: "",
      selectedNodeId: "",
      possibleMoves: [],
      selectedOption: [],

    }
  },
  methods: {

    
    moveNode: function(){
    
      axios.post('http://127.0.0.1:8001/api/node/move/'+this.selectedNodeId, {
        _method: "put",
        id: this.selectedOption
      })
          .then(function( response ){
            if(response.data.message){
              alert(response.data.message);
            }
              this.getData();
          }.bind(this));
    }, 
    collapseNodes: function(){
      this.clickedItems = [];
      localStorage.setItem('clickedItems', '');
      this.getData();
    },
    expandNodes: function(){
          axios.get(`http://127.0.0.1:8001/api/node/getClicked`)
        .then(response => {
          
         
          const ids = [];
          response.data.list.forEach(element => {
            if(element.is_node && element.children)
              ids.push(element.id);
          });
          
          this.clickedItems = ids;
          this.setClickedElement(ids, true);
        })
        },
    setClickedElement: function(ids, clickAll = false){
      
      const idMap = this.nodeArr.reduce(function merge(map, node) {
        map[node.id] = node;
        
        if (Array.isArray(node.children)) {
          node.children.reduce(merge, map);
        }
        
        return map;
      }, {});

      const items = ids.map(id => idMap[id]);
      
      items.forEach(item => {
        
        
          
    if(item)
        if(item.children){
          if(clickAll && item.is_node){
            item.clicked = true;
          }

          else if(!item.clicked){
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
        this.addNewNode(event.target.id, 0);
      }else if(event.target.id && event.target.className == "edit"){
        this.editNode(event.target.id);
      }else if(event.target.id && event.target.className == "sort"){
        this.sortChildren([event.target.id]);
      }
   
    },
    editNode: function(nodeId){

      let newName = prompt("Change name");
      if(newName){
      axios.post('http://127.0.0.1:8001/api/node/edit/'+nodeId, {
        _method: "put",
        name: newName
      })
          .then(function(response){
            if(response.data.message){
              alert(response.data.message);
            }else
              this.getData();
          }.bind(this));
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
      
      if(!response.data.message){
          this.nodeArr = response.data;
          this.items = this.nodeArr;

          this.setClickedElement(localStorage.getItem('clickedItems').split(','));
    
      }else{
        this.items = [];
        localStorage.setItem("clickedItems", '');
      }
      
    })
    },

    sortChildren: function(ids){
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
          item.children.sort((a, b) => {
          let fa = a.name.toLowerCase(),
              fb = b.name.toLowerCase();

          if (fa < fb) {
              return -1;
          }
          if (fa > fb) {
              return 1;
          }
          return 0;
          })
        }
      });
    },
    addNewNode: function(nodeId, isNode){
      let nodeName;
      if(isNode)
        nodeName = prompt("Wpisz nazwe folderu", "Folder");
      else
        nodeName = prompt("Wpisz nazwe pliku", "Plik");

      if(nodeName){
      if(nodeId == "0")
        nodeId = null;


      axios.post('http://127.0.0.1:8001/api/node/add', {
        name: nodeName,
        id: nodeId,
        is_node: isNode
      })
          .then(function( response ){
            if(response.data.message){
              alert(response.data.message);
            }
              
              this.getData();
          }.bind(this));
      }

    },

    removeNode: function(nodeId){
      
      axios.post('http://127.0.0.1:8001/api/node/remove/'+nodeId, {
        _method: "delete"
      })
          .then(function(){
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

.mainButtons button{
  
  max-width: 20%;
  width:100%;
  margin-top: 10px;
  margin-bottom: 10px;
}


</style>