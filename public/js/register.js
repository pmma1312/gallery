import navbar from './components/navigation.js';

const app = new Vue({
    el: "#root",
    data: {
        username: "",
        password: "",
        passwordConfirm: ""
    },
    components: {
        navbar
    },
    methods: {
        register(e) {
            e.preventDefault();

            if(this.password === this.passwordConfirm) {
                let formData = new FormData();
                formData.append("username", this.username);
                formData.append("password", this.password);

                axios.post("/api/user/create", formData)
                .then(response => {
                    setCookie("token", response.data.data.token, "30");

                    Swal.fire(
                        "Success!",
                        "The registration has been successful!",
                        "success"
                    ).then(() => {
                        window.location.replace("/panel");
                    }); 
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
                    "Error!",
                    "Your passwords don't match.",
                    "error"
                );
            }
        }
    }
});