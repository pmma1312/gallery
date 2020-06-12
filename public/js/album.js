import navbar from './components/navigation.js';
import modal from "./components/modal.js";

const app = new Vue({
    el: "#root",
    data: {
        album: {
            album: {
                id: 0,
                name: "Not Found",
                thumbnail_id: 0,
                created_at: "01-01-01 01:01:01"
            },
            images: []
        },
        position: 0,
        isPlaying: false,
        slideshow: ""
    },
    components: {
        navbar,
        modal
    },
    methods: {
        loadImages() {
            axios.get(`/api/album/${this.getAlbumName()}`)
            .then(response => {
                this.album = response.data.data;
            })
            .catch(error => {
                console.log(error);
            });
        },
        showModal(path) {
            this.$refs.modal.showModal(path);
            this.position = this.getImageIndex();
        },
        getAlbumName() {
            let url = new URL(window.location.href);
            return url.pathname.replace("/album/", "");
        },
        previousImage() {
            let index = this.getImageIndex();

            if((index - 1) > -1) {
                this.position = index - 1;
                this.$refs.modal.modalImage = this.album.images[index - 1].path;
            }
        },
        nextImage() {
            let index = this.getImageIndex();

            if((index + 1) < this.album.images.length) {
                this.position = index + 1;
                this.$refs.modal.modalImage = this.album.images[index + 1].path;
            }
        },
        getImageIndex() {
            return this.album.images.findIndex(image => image.path == this.$refs.modal.modalImage);
        },
        keydown(e) {
            if(e.keyCode == 37) {
                // Left
                this.previousImage();
            } else if(e.keyCode == 39) {
                // Right
                this.nextImage();
            } else if(e.keyCode == 27) {
                this.$refs.modal.hideModal();
            }
        },
        playSlideshow() {
            var index = this.getImageIndex();   
            var instance = this;

            if((index + 1) < this.album.images.length) {
                this.isPlaying = true;

                this.slideshow = setInterval(function() {
                    if((index + 1) < instance.album.images.length) {
                        instance.nextImage();
                    } else {
                        instance.isPlaying = false;
                        clearInterval(instance.slideshow);
                    }

                    index++;
                }, 4000);
            } else {
                if(this.album.images[0].path) {
                    this.position = 0;
                    this.$refs.modal.modalImage = this.album.images[0].path;
                    this.playSlideshow();
                }
            }
        },
        stopSlideshow() {
            if(this.isPlaying) {
                clearInterval(this.slideshow);
                this.isPlaying = false;
            }
        },
        async downloadAlbum() {
            let zip = new JSZip();

            Swal.fire({
                title: '',
                html: `
                <img src='/public/img/icons/loadingImages.gif' style='width: 60px; height: auto;'>
                <p>Please wait while we prepare the album for you...</p>
                `,
                showConfirmButton: false,
                allowOutsideClick: false
            });

            for(let i = 0; i < this.album.images.length; i++) {
                await axios.get(`/public/img/uploads/${this.album.images[i].path}`, {
                    responseType: 'blob',
                })
                .then(response => {
                    if(response.data) {
                        zip.file(this.album.images[i].path, response.data, {
                            binary: true
                        });
                    }
                })
                .catch(error => {});
            }

            await zip.generateAsync({
                type: "blob"
            }).then((content) => {
                var uriContent = URL.createObjectURL(content);

                let downloadLink = document.createElement("a");
                downloadLink.download = this.album.album.name + ".zip";
                downloadLink.href = uriContent;

                document.body.appendChild(downloadLink);

                downloadLink.click();

                document.body.removeChild(downloadLink);
            });

            Swal.fire({
                timer: 1,
                showConfirmButton: false
            });
        }
    },
    mounted() {
        this.loadImages();
        document.addEventListener("keydown", this.keydown);
    }
});