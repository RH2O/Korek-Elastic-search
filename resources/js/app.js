import './bootstrap';

import { createApp } from "vue/dist/vue.esm-bundler";

import Index from "@/pages/Index.vue";
import Search from "@/pages/Search.vue";

const app = createApp({});

// Register MyComponent globally
app.component("my-index", Index);
app.component("my-search", Search);

app.mount("#app");
