<template>
  <div>
    <ul>
      <li v-for="i of items" :key="i.id"  >
        
        <p v-bind:id="i.id" 
        :class="i.children ? 'parent' : ''" 
        v-if="i.clicked"> 
        
        <font-awesome-icon icon="chevron-down" /> 
        {{i.name}}<button v-bind:id="i.id">+</button> <button class='delete' v-bind:id="i.id">-</button></p>

        <p v-bind:id="i.id" 
        :class="i.children ? 'parent' : ''" 
        v-else-if="i.children"> 
        <font-awesome-icon icon="chevron-right" />
         {{i.name}}<button v-bind:id="i.id">+</button> <button class='delete' v-bind:id="i.id">-</button></p>

        <p v-bind:id="i.id" 
        :class="i.children ? 'parent' : ''" 
        v-else> 
        {{i.name}} <button v-bind:id="i.id">+</button> <button class='delete' v-bind:id="i.id">-</button></p>


      
        
        <Tree :items="i.children" 

        v-if="i.children && i.clicked"/>
        
      </li>
    </ul>
  </div>
</template>

<script>


export default {
  name: "Tree",
  props: ['items']
  
};
</script>


<style >
  
  ul{
    list-style: none;
    
  }

  li{
    cursor: pointer;
  }
  .parent{
    color: red;
  }
</style>