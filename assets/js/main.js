Vue.component('bubblesort', {
  template: `
    <div class="bubblesort-container">
     <h1 class="text-center">BubbleSort Simulation</h1>
     <div class="bs-header text-center">
      <button class="btn btn-primary" @click="shuffle" v-bind:disabled="playing">Shuffle</button>
      <button class="btn btn-secondary" @click="step" v-bind:disabled="completed || playing">Step</button>
      <button class="btn btn-success" @click="play" v-if="playing">Pause</button>
      <button class="btn btn-success" @click="play" v-else v-bind:disabled="completed">Play</button>
     </div>
     <h4>Total of comparisons: {{comparisons}}</h4>
     <div>
      <table>
        <tr>
          <td>Index</td>
          <td v-for="value,index in values">{{index}}</td> 
        </tr>
        <tr>
          <td>Value</td>
          <td v-for="value,index in values">{{value}}</td> 
        </tr>
      </table>
     </div>

     <br>
     <table>
      <tr v-for="value, index in values">
        <td>
          <div v-bind:style="{ background: (selected && (index == current.index || index == current.index +1)? hcolor : color), width: (value*2) + 'px' }"></div>
        </td>
      </tr>
     </table
    </div>
  `,
  props:[
    'len','hcolor','color'
  ],
  data() {
    return {
      ready : false,
      playing: false,
      completed: false,
      selected: false,
      comparisons: 0,
      values: [],
      current: {
        index : 0,
        limit: 0,
        nswap: 0,
      }
    }
  },
  methods:{
    shuffle(){
      this.completed = false;
      this.playing = false;
      this.comparisons = 0;
      this.current = {
        index: 0,
        limit: this.len,
        nswap: 0,
      }

      var vm = this;
      jQuery.get('/test/bubblesort/shuffle', function(response){
        vm.values = response.data;
        vm.ready = true;
      });
    },
    step(){
      if(this.ready){

        if(!this.selected){
          this.comparisons++;
          this.selected = true;
          var vm = this;
          var values = [vm.values[vm.current.index],vm.values[vm.current.index+1]];
          
          jQuery.post('/test/bubblesort/step',{current: vm.current,values: values },function(response){
            var action = response.data;

            if(action.swap){
              var t = vm.values[vm.current.index];
              vm.values[vm.current.index] = vm.values[vm.current.index+1];
              vm.values[vm.current.index+1] = t;
            }

            setTimeout(function(){
              vm.selected = false;
              vm.current = action.current;
              vm.completed = action.completed;

              if(vm.completed){
                vm.playing = false;
              }

              if(vm.playing){
                vm.step();
              }
            },300);
          });
        }
      } else {
        alert('You must first shuffle');
      }
    },
    play(){
      if(this.ready){
        this.playing = !this.playing;
        if(this.playing){
          this.step();
        }
      } else {
        alert('You must first shuffle');
      }
    },
  }
});


new Vue({
  el: '#vue-app',
})

