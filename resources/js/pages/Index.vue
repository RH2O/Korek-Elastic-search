<template>

    <div class="search">
        <a href="/search" class="btn btn-primary">Search</a>
    </div>

    <div class="container">
        <div v-if="showSuccessAlert" class="success-alert">
        Files uploaded successfully!
    </div>

    <div class="mt-5 py-3">
        <h3>Upload PDF Files</h3>
    </div>

    <div>
      <input type="file" ref="fileInput" multiple @change="handleFileChange" />
      <button @click="uploadFiles" class="px-2 mx-5">Upload</button>
    </div>

    <div class="mt-5">
        <h4>Indexed Documents :</h4>

        <div v-for="item in result">
            <a :href=item.file_path><p class="alert alert-info">{{item.title}}</p></a>
        </div>

    </div>
    </div>

  </template>

  <script>
  export default {
    data() {
      return {
        selectedFiles: null,
        showSuccessAlert:false,
        result: null,
      };
    },
    methods: {
      handleFileChange(event) {
        this.selectedFiles = event.target.files;
      },
      async uploadFiles() {
        if (!this.selectedFiles || this.selectedFiles.length === 0) {
          return;
        }

        const formData = new FormData();

        for (let i = 0; i < this.selectedFiles.length; i++) {
          formData.append("pdf_files[]", this.selectedFiles[i]);
        }

        try {
          const response = await axios.post("/upload", formData, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          });

          this.showSuccessAlert = true;

          setTimeout(() => {
            this.showSuccessAlert = false; // Hide the success alert after 3 seconds
         },3000);

         this.fetchData();

        } catch (error) {
          console.error("Upload failed", error);
        }
      },

    fetchData() {
        var url = `/search-data`;
        axios.get(url).then((res) => {
            // console.log(res);
            this.result = res.data
        })
        .catch((error) => {

        });
    },

    },

    created(){
        this.fetchData();
    }

  };
  </script>

<style>
/* Add your styles here */
.success-alert {
  padding: 10px;
  background-color: #aaffaa;
  border: 1px solid #00aa00;
  border-radius: 5px;
  margin-top: 10px;
}

a{
    font-weight: bold;
    text-decoration: none;

}

.search{
    position: absolute;
    right: 50px;
    top: 20px;
    padding: 20px;
}

</style>
