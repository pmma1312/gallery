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
        }
    },
    template: `
    <transition name="out">
        <div class="image-modal" v-show="showImageModal">
            <div class="row mb-2">
                <div class="col-12 text-right">
                    <img src="/public/img/icons/delete.png" alt="close" class="icon-delete m-2 mr-4" @click="hideModal" title="close">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <img :src="\`/public/img/uploads/\${modalImage}\`" alt="image" class="modalImage mx-auto d-block">
                </div>
            </div>
        </div>
    </transition>
    `
};