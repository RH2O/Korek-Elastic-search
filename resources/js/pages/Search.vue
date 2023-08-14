<template>
    <div class="container">

        <div class="input-group my-5" style="width: 400px; margin: auto;">
            <input type="text" class="form-control" placeholder="Search" v-model="search_text" dir="rtl">
            <button class="btn btn-info" type="button" id="button-addon2" @click="fetchData()">Search</button>
        </div>


        <div v-if="!loadingStatus">
                <div
                v-for="doc in docs"
                :key="doc._id"
                class="card mb-4"
                >
                <div class="card-header d-flex justify-content-between">
                <a :href="doc.file_path"><p class="btn btn-primary p-1">Preview PDF</p></a>
                <p class="p-3" style="font-weight: bold;">{{ doc.title }}</p>
                </div>
                <div class="card-body p-4">
                <template v-if="search_text !== ''">
                    <span v-html="highlightedContent(doc.attachment?.content)"></span>
                </template>
                </div>
            </div>
        </div>

        <div v-else >
            <div class="text-center mt-5 pt-5">
                <div class="spinner-border" style="width: 5rem; height: 5rem;" role="status">

                </div>
            </div>
        </div>

    </div>
</template>


<script>

export default {
    components: {
    },
    data() {
        return {
            search_text: null,
            docs: [],
            loadingStatus:false,
        };
    },
    methods: {
        fetchData() {
            var url = `/search-data?search_text=${this.search_text}`;
            this.loadingStatus = true;
            axios.get(url).then((res) => {
                this.docs = res.data
                this.loadingStatus = false
            })
                .catch((error) => {
                    this.loadingStatus = false
                });
        },

        highlightedContent(content) {
      if (!content) {
        return '';
      }

      const index = content.toLowerCase().indexOf(this.search_text.toLowerCase());
      if (index !== -1) {
        const startIndex = Math.max(0, index - 5);
        const endIndex = Math.min(index + this.search_text.length + 10, content.length);

        const beforesearch_text = content.substring(startIndex, index);
        const highlightedsearch_text = content.substring(index, index + this.search_text.length);
        const aftersearch_text = content.substring(index + this.search_text.length, endIndex);

        return `
          ${startIndex > 0 ? '...' : ''}
          ${beforesearch_text}
          <span class="bg-warning text-white px-2 py-1 rounded">
            ${highlightedsearch_text}
          </span>
          ${aftersearch_text}
          ${endIndex < content.length ? '...' : ''}
        `;
      } else {
        return content.length > 60 ? `${content.substring(0, 60)}...` : content;
      }
    },

    },
    mounted() {
        // Start the Fetching API
        this.fetchData();
    },
};
</script>


<style scoped>
.search {
    width: 80% auto;
}
</style>
