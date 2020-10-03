<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <base-station></base-station>
            </div>
        </div>
    </div>
</template>

<script>
  import axios from "axios";
  import Basestation from "./Basestation";

  export default {
    components: {
      "base-station": Basestation 
    },

    mounted() {
        console.log('Component mounted.')
    }
  }
</script>
