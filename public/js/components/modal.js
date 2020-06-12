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
        }
    },
    template: `
    <transition name="out">
        <div class="image-modal" v-show="showImageModal">
            <div class="row h-100 d-flex">
                <div class="col-12 mt-2">
                    <div class="row">
                        <div class="col-4 text-left">
                            <img src="/public/img/icons/download.png" alt="download" class="icon-modal m-2" @click="downloadImage" title="download">
                        </div>
                        <div class="col-4 text-center">
                            <slot name="additional"></slot>
                        </div>
                        <div class="col-4 text-right">
                            <img src="/public/img/icons/delete.png" alt="close" class="icon-modal m-2 mr-4" @click="hideModal" title="close">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row mb-2">
                        <div class="col-12">
                            <img :src="\`/public/img/uploads/\${modalImage}\`" alt="image" class="modalImage mx-auto d-block">
                        </div>
                    </div>
                </div>
            </div>
            <slot name="bottom" class="row"></slot>
        </div>
    </transition>
    `
};