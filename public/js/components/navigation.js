export default {
    name: 'navbar',
    data: function() {
        return {
            activeSite: this.getActiveSite(),
            sites: [
                {
                    name: "Home",
                    route: "/",
                    icon: "/public/img/icons/home.png"
                },
                {
                    name: "Albums",
                    route: "/albums",
                    icon: "/public/img/icons/albums.png"
                },
                {
                    name: "Images",
                    route: "/images",
                    icon: "/public/img/icons/images.png"
                },
            ]
        }
    },
    methods:  {
        getActiveSite() {
            const url = new URL(window.location.href);
            return url.pathname;
        }
    },
    template: `
        <div class="navbar navbar-expand-lg navbar-dark mt-3 rounded">
            <a class="navbar-brand">pGallery</a>
            <div class="navbar">
                <ul class="navbar-nav">
                    <a :href="(site.route != activeSite) ? site.route : '#'" class="nav item nav-link" v-for="site in sites" :title="site.name" v-bind:class="{ active: activeSite == site.route }">
                        <img :src="site.icon" class="icon-navbar">
                    </a>
                </ul>
            </div>
        </div>
    `
};