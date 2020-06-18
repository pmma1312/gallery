import navbar from './components/navigation.js';
import modal from './components/modal.js';

const app = new Vue({
    el: "#root",
    data: {
        images: [],
        limit: 20,
        offset: 0,
        isLoading: false,
        noNewData: 0,
        position: 0
    },
    components: {
        navbar,
        modal
    },
    methods: {
        loadImages() {
            if(this.noNewData < 3) {
                this.isLoading = true;
                axios.get(`/api/images/${this.limit}/${this.offset}`)
                .then(response => {
                    if(response.data.data) {
                        response.data.data.forEach(element => {
                            this.images.push(element);
                        });
    
                        this.offset += this.limit;
                        this.noNewData = 0;
                    } else {
                        this.noNewData++;
                    }
                })
                .catch(error => {
                    if(error.response) {
                        Swal.fire(
                            "Error!",
                            error.response.data.message,
                            "error"
                        );
                    }
                })
                .finally(() => {
                    this.isLoading = false;
                });
            }
        },
        showModal(path) {
            this.$refs.modal.showModal(path);
            this.position = this.getImageIndex();
        },
        infinityScroll() {
            window.onscroll = () => {
                let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;
            
                if (bottomOfWindow) {
                    this.loadImages();
                }
            }
        },
        previousImage() {
            let index = this.getImageIndex();

            if((index - 1) > -1) {
                this.position = index - 1;
                this.$refs.modal.modalImage = this.images[index - 1].path;
            }
        },
        nextImage() {
            let index = this.getImageIndex();

            if((index + 1) < this.images.length) {
                this.position = index + 1;
                this.$refs.modal.modalImage = this.images[index + 1].path;
            } else {
                this.loadImages();
            }
        },
        getImageIndex() {
            return this.images.findIndex(image => image.path == this.$refs.modal.modalImage);
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
        this.infinityScroll();
        document.addEventListener("keydown", this.keydown);
    }
});