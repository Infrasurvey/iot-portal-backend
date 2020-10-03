<template>
  <div>
      <h2 class="mt-4">Base station 1 rower</h2>
      <b-table
        v-if="loaded"
        striped
        hover
        :items="basestation.rower"
      >
      </b-table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      loaded: false,
      basestation: null,
      basestation_id: this.$route.params.basestation_id
    }
  },
  methods: {
    loadInfos() {
      axios
        .get("/api/basestation/" + this.basestation_id)
        .then((resp) => {
          this.basestation = resp.data
          this.loaded = true
        })
        .catch((error) => {
          console.log(error)
        })
    }
  },
  mounted() {
    this.loadInfos()
  }
}
</script>
<style scoped>
</style>
