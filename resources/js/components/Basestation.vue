<template>
  <div>
      <h2>Base stations</h2>
      <b-table
        v-if="loaded"
        striped
        hover
        :items="basestations.datas"
        :fields="basestations.fields"
      >
      </b-table>

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
      basestations: {
        datas: null,
        fields: [
          {
            key: "id",
            label: "#",
            sortable: true,
          },
          {
            key: "name",
            label: "Name",
            sortable: true,
          }
        ],
      },
    }
  },
  methods: {
    loadTest() {
      axios
        .get("/api/basestations")
        .then((resp) => {
          this.basestations.datas = resp.data
        })
        .catch((error) => {
          console.log(error)
        })
    },

    loadInfos() {
      axios
        .get("/api/basestation/1")
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
    this.loadTest()
    this.loadInfos()
  }
}
</script>
<style scoped>
</style>
