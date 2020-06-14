export default {
    name: 'modal',
    data: function() {
        return {
            modalImage: "",
            showImageModal: false
        }
    },
    methods:  {
        hideModal() {
            if(this.showImageModal)
                this.showImageModal = false;
        },
        showModal(path) {
            if(!this.showImageModal) {
                this.modalImage = path;
                this.showImageModal = true;
            }
        },
        downloadImage() {
            if(this.modalImage) {
                let link = document.createElement('a');
                link.href = `/public/img/uploads/${this.modalImage}`;
                link.download = this.modalImage;

                document.body.appendChild(link);

                link.click();

                document.body.removeChild(link);
            }
        },
        keydown(e) {
            if(e.keyCode == 27) {
                this.hideModal();
            }
        },
        makeFullScreen() {
            var divObj = this.$refs.imgModal;

            if (divObj.requestFullscreen) {
                divObj.requestFullscreen();
            } else if (divObj.msRequestFullscreen) {
                divObj.msRequestFullscreen();               
            } else if (divObj.mozRequestFullScreen) {
                divObj.mozRequestFullScreen();      
            } else if (divObj.webkitRequestFullscreen) {
                divObj.webkitRequestFullscreen();       
            } else {
                console.log("Fullscreen API is not supported");
            }
        } 
    },
    template: `
    <transition name="out">
        <div class="image-modal" v-show="showImageModal">
            <div class="row h-100 d-flex">
                <div class="col-12 my-modal-header">
                    <div class="row">
                        <div class="col-4 text-left">
                            <img src="/public/img/icons/download.png" alt="download" class="icon-modal m-2" @click="downloadImage" title="download">
                        </div>
                        <div class="col-4 text-center align-self-center">
                            <slot name="additional"></slot>
                        </div>
                        <div class="col-4 text-right">
                            <img src="/public/img/icons/delete.png" alt="close" class="icon-modal m-2 mr-4" @click="hideModal" title="close">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row mb-2">
                        <div class="col-12 d-flex align-items-center justify-content-center">
                            <img :src="\`/public/img/uploads/\${modalImage}\`" alt="image" class="modalImage mx-auto d-block" @click="makeFullScreen" ref="imgModal">
                        </div>
                    </div>
                </div>
            </div>
            <slot name="bottom" class="row"></slot>
        </div>
    </transition>
    `,
    mounted() {
        document.addEventListener("keydown", this.keydown);
    }
};