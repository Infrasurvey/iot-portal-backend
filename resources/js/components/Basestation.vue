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
          },
          {
            key: "DeviceRoverCount",
            label: "Number of Rovers"
          }
        ],
      },
    }
  },
  methods: {
    onClick(item, index, event) {
      this.$router.push({ name: 'DeviceRoverCount ', params: { basestation_id: item.id } })
    },

    loadTest() {
      axios
        .get("/api/basestations")
        .then((resp) => {
          console.log(resp.data)
          this.basestations.datas = resp.data
          this.loaded = true;
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