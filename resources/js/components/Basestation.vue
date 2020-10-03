<template>
  <div>
      <h2>Base stations</h2>
      <b-table
        v-if="loaded"
        striped
        hover
        :items="basestations.datas"
        :fields="basestations.fields"
        @row-clicked="onClick"
      >
      </b-table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      loaded: false,
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
    onClick(item, index, event) {
      this.$router.push({ name: 'rower', params: { basestation_id: item.id } })
    },

    loadTest() {
      axios
        .get("/api/basestations")
        .then((resp) => {
          this.basestations.datas = resp.data
          this.loaded = true
        })
        .catch((error) => {
          console.log(error)
        })
    }
  },
  mounted() {
    this.loadTest()
  }
}
</script>
<style scoped>
</style>
