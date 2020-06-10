import navbar from './components/navigation.js';
import modal from './components/modal.js';

const app = new Vue({
    el: "#root",
    data: {
        images: [],
    },
    components: {
        navbar,
        modal
    },
    methods: {
        loadImages() {
            axios.get("/api/user/images")
            .then(response => {
                if(response.data.data) {
                    this.images = response.data.data;
                }
            })
            .catch(error => {
                console.log(error);
            });
        },
        uploadFiles(e) {
            e.preventDefault();

            let formData = new FormData();

            if(this.$refs.files.files.length < 10) {
                for(let i = 0; i < this.$refs.files.files.length; i++) {
                    formData.append("files[]", this.$refs.files.files[i]);
                }
    
                axios.post("/api/files/upload", formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })
                .then(response => {
                    if(response.data.data.error && response.data.data.error > 0) {
                        Swal.fire(
                            "Warning!",
                            response.data.data.error.join("\n"),
                            "warning"
                        );
                    } else {
                        Swal.fire(
                            "Success!",
                            response.data.message,
                            "success"
                        );  
                    }

                    // Prepend fresh uploaded images to images array
                    if(response.data.data.images) {
                        console.log(response.data.data);
                        this.images = response.data.data.images.concat(this.images);
                    }

                    e.target.reset();
                })
                .catch(error => {
                    if(error.response) {
                        Swal.fire(
                            "Error!",
                            error.response.data.message,
                            "error"
                        );
                    }
                });
            } else {
                Swal.fire(
                    "Warning!",
                    "Please don't upload more than 10 files per upload.",
                    "warning"
                );

                e.target.reset();
            }
        },
        deleteImage(id) {
            axios.delete(`/api/image/${id}`)
            .then(response => {
                this.images = this.images.filter(item => item.id != id);
            })
            .catch(error => {
                if(error.response) {
                    Swal.fire(
                        "Error!",
                        error.response.data.message,
                        "error"
                    );
                }
            });
        }
    },
    mounted() {
        this.loadImages();
    }
});