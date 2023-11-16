import { ref } from "vue";
import { useRouter } from "vue-router";

export default function usePosts() {
    const posts = ref({});
    const post = ref({});
    const router = useRouter();
    const validationErrors = ref({});
    const isLoading = ref(false);

    const getPost = async (id) => {
        axios.get("/api/posts/" + id).then((response) => {
            post.value = response.data.data;
            // console.log(response.data.data);
        });
    };

    const storePost = async (post) => {
        if (isLoading.value) return;

        isLoading.value = true;
        validationErrors.value = {};
        let serializedPost = new FormData();
        for (let item in post) {
            if (post.hasOwnProperty(item)) {
                serializedPost.append(item, post[item]);
            }
        }
        console.log(serializedPost);

        axios
            .post("/api/posts", serializedPost)
            .then((response) => {
                router.push({ name: "posts.index" });
            })
            .catch((error) => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors;
                    isLoading.value = false;
                }
            });
        // console.log(post);
    };

    const updatePost = async (post) => {
        
        if (isLoading.value) return;
        isLoading.value = true;
        validationErrors.value = {};
        let serializedPost = new FormData();
        for (let item in post) {
            if (post.hasOwnProperty(item)) {
                serializedPost.append(item, post[item]);
            }
        }
        // const postItem = {
        //     title: post.title,
        //     content: post.content,
        //     category_id: post.category_id,
        //     thumbnail: "2025-11-14 11:21:55_655358532fcca.Screenshot 2023-11-01 at 1.13.53 PM.png"
        // }
        console.log(post);
        axios
            .patch(`/api/posts/${post.id}`, serializedPost)
            .then((response) => {
                router.push({ name: "posts.index" });
            })
            .catch((error) => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors;
                }
            })
            .finally(() => (isLoading.value = false));
    };

    const getPosts = async (
        page = 1,
        category = "",
        order_column = "created_at",
        order_direction = "desc"
    ) => {
        axios
            .get(
                "/api/posts?page=" +
                    page +
                    "&category=" +
                    category +
                    "&order_column=" +
                    order_column +
                    "&order_direction=" +
                    order_direction
            )
            .then((response) => {
                posts.value = response.data;
            });
    };
    return {
        posts,
        post,
        getPosts,
        getPost,
        storePost,
        updatePost,
        validationErrors,
        isLoading,
    };
}
