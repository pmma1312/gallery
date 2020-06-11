import navbar from './components/navigation.js';
import modal from "./components/modal.js";

const app = new Vue({
    el: "#root",
    data: {
        album: {
            name: "Not found",
            images: []
        },
        position: 0
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
            } 
        }
    },
    mounted() {
        this.loadImages();
        document.addEventListener("keydown", this.keydown);
    }
});