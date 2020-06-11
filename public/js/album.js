import navbar from './components/navigation.js';
import modal from "./components/modal.js";

const app = new Vue({
    el: "#root",
    data: {
        album: {
            name: "Not found",
            images: []
        },
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
        getAlbumName() {
            let url = new URL(window.location.href);
            return url.pathname.replace("/album/", "");
        }
    },
    mounted() {
        this.loadImages();
    }
});