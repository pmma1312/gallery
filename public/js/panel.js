import navbar from './components/navigation.js';
import modal from './components/modal.js';
import albummodal from './components/album_modal.js';

const app = new Vue({
    el: "#root",
    data: {
        images: [],
        albums: [],
        limit: 15,
        offset: 0,
        noNewData: 0,
        isLoading: false,
        albumName: "New Album",
        modalAlbumBtnText: "Create Album"
    },
    components: {
        navbar,
        modal,
        albummodal
    },
    methods: {
        loadImages() {
            if(this.noNewData < 3) {
                this.isLoading = true;
                axios.get(`/api/user/images/${this.limit}/${this.offset}`)
                .then(response => {
                    if(response.data.data) {
                        response.data.data.forEach(item => {
                            let exists = this.images.find((x) => {
                                return x.id == item.id;
                            });
                            
                            if(!exists) {
                                this.images.push(item);
                            }
                        });

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
        loadAlbums() {
            axios.get("/api/user/albums")
            .then(response => {
                if(response.data.data) {
                    this.albums = response.data.data;
                }
            })
            .catch(error => { console.log(errror) });
        },
        resolveAction() {
            if(this.modalAlbumBtnText == "Create Album") {
                this.createAlbum();
            } else {
                this.updateAlbum();
            }
        },
        createAlbum() {
            let ids = [];

            if(!this.albumName) {
                Swal.fire(
                    "Error!",
                    "Please enter an album name!",
                    "error"
                );
                return;
            }

            if(!this.$refs.albummodal.thumbnail) {
                Swal.fire(
                    "Error!",
                    "Please select a thumbnail!",
                    "error"
                );
                return;
            }

            for(let i = 0; i < this.$refs.albummodal.checkBoxes.length; i++) {
                if(this.$refs.albummodal.checkBoxes[i]) {
                    ids.push(i);
                }
            }


            let data = JSON.stringify({
                "image_ids": ids,
                "name": this.albumName,
                "thumbnail": this.$refs.albummodal.thumbnail
            });

            let formData = new FormData();
            formData.set("json", data);

            axios.post("/api/album/create", formData)
            .then(response => {
                Swal.fire(
                    "Success!",
                    "Your album has been created!",
                    "success"
                );

                this.$refs.albummodal.checkBoxes = [];
                this.$refs.albummodal.thumbnail = "";
                this.albumName = "";

                if(response.data.data) {
                    this.albums = response.data.data.concat(this.albums);
                }
            })
            .catch(error => {
                if(error.response) {
                    if(!error.response.data.data) {
                        Swal.fire(
                            "Error!",
                            error.response.data.message,
                            "error"
                        );
                    } else {
                        Swal.fire(
                            "Error!",
                            error.response.data.data.join("\n"),
                            "error"
                        );
                    }
                }
            });
        },
        updateAlbum() {

        },
        deleteAlbum(id) {
            axios.delete(`/api/album/${id}`)
            .then(response => {
                Swal.fire(
                    "Success!",
                    response.data.message,
                    "success"
                );

                this.albums = this.albums.filter(item => item.id != id);
            })
            .catch(error => {
                Swal.fire(
                    "Error!",
                    error.response.data.message,
                    "error"
                );
            });
        },
        editAlbum(name) {
            axios.get(`/api/album/${name}`)
            .then(response => {
                // TODO: SAVE EDITED ALBUM

                response.data.data.images.forEach(item => {
                    this.$refs.albummodal.checkBoxes[item.id] = true;
                });

                this.showAlbumModal();

                this.modalAlbumBtnText = "Update Album"

                this.albumName = name;
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
                let bottomOfDiv = this.$refs.imagesContainer.scrollTop + this.$refs.imagesContainer.clientHeight ===  this.$refs.imagesContainer.scrollHeight;

                if (bottomOfDiv) {
                    this.loadImages();
                }
            }
        },
        showAlbumModal() {
            this.modalAlbumBtnText = "Create Album"
            this.$refs.albummodal.showModal();
        }
    },
    mounted() {
        this.loadImages();
        this.loadAlbums();
        this.infinityScroll();
    }
});