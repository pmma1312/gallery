import navbar from './components/navigation.js';
import modal from './components/modal.js';

const app = new Vue({
    el: "#root",
    data: {
        images: [],
        limit: 15,
        offset: 0,
        noNewData: 0,
        isLoading: false
    },
    components: {
        navbar,
        modal
    },
    methods: {
        loadImages() {
            if(this.noNewData < 3) {
                this.isLoading = true;
                axios.get(`/api/user/images/${this.limit}/${this.offset}`)
                .then(response => {
                    if(response.data.data) {
                        this.images = this.images.concat(response.data.data);

                        this.offset += this.limit;
                        this.noNewData = 0;
                    } else {
                        this.noNewData++;
                    }
                })
                .catch(error => {
                    console.log(error);
                })
                .finally(() => {
                    this.isLoading = false;
                });
            }
        },
        uploadFiles(e) {
            e.preventDefault();

            let formData = new FormData();

            if(this.$refs.files.files.length < 10) {
                for(let i = 0; i < this.$refs.files.files.length; i++) {
                    formData.append("files[]", this.$refs.files.files[i]);
                }
    
                axios.post("/api/files/upload", formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                    onUploadProgress: (e) => {
                        Swal.fire({
                            title: '',
                            html: `
                            <img src='/public/img/icons/loading.gif' style='width: 128px; height: auto;'>
                            <p>Please wait while we upload your images...</p>
                            `,
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });
                    }
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
            Swal.fire({
                title: "Are you sure?",
                text: "Your image will forever be lost.",
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No don't"
            }).then((result) => {
                if(result.value) {
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
            });
        },
        infinityScroll() {
            this.$refs.imagesContainer.onscroll = () => {
                let bottomOfWindow = this.$refs.imagesContainer.scrollTop + this.$refs.imagesContainer.clientHeight ===  this.$refs.imagesContainer.scrollHeight;

                if (bottomOfWindow) {
                    this.loadImages();
                }
            }
        }
    },
    mounted() {
        this.loadImages();
        this.infinityScroll();
    }
});