export default {
    name: 'albummodal',
    data: function() {
        return {
            showAlbumModal: false,
            images: [],
            checkBoxes: [],
            thumbnail: 0
        }
    },
    methods:  {
        showModal() {
            this.showAlbumModal = true;
        },
        hideModal() {
            this.showAlbumModal = false;
        },
        loadImages() {
            axios.get(`/api/user/images`)
            .then(response => {
                if(response.data.data) {
                    this.images = response.data.data;
                }
            })
            .catch(error => {
                console.log(error);
            });
        },
        setThumbnail(thumbnail) {
            this.thumbnail = thumbnail;
        }
    },
    template: `
    <transition name="out">
        <div class="image-modal p-4" v-show="showAlbumModal">
            <div class="row mb-2">
                <div class="col-12 text-right">
                    <img src="/public/img/icons/delete.png" alt="close" class="icon-modal m-2" @click="hideModal" title="close">
                </div>
            </div>
            <slot name="additional" class="row m-2"></slot>
            <div class="row m-3 image-selection">
                <div class="album-modal-col" v-for="image in images">
                    <div class="card bg-dark">
                        <div class="card-body text-center">
                            <div class="form-group">
                                <input type="checkbox" class="album-checkbox" :id="image.id" v-model="checkBoxes[image.id]"></input>
                            </div>
                            <img class="img-album-sm" :src="\`/public/img/uploads/\${image.path}\`">
                            <div class="row mt-2">
                                <div class="col-12 text-center">
                                <button class="btn btn-primary" @click="setThumbnail(image.id)" :class="{ 'thumbnailSelected' : thumbnail == image.id }">Set as thumbnail</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <slot name="below"></slot>
        </div>
    </transition>
    `,
    mounted() {
        this.loadImages();
    }
};