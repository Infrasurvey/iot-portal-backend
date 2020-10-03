import Vue from "vue"
import VueRouter from "vue-router"

Vue.use(VueRouter)

import Home from './components/HomeComponent'
import Rower from './components/Rower'

const routes = [
  {
    path: "/",
    name: "home",
    component: Home
  },
  {
    path: "/",
    name: "rower",
    component: Rower
  }
];

const router = new VueRouter({
  history: true,
  base: "/",
  mode: "history",
  routes
});

router.beforeResolve((to, from, next) => {
  next();
});

router.afterEach((to, from) => {
});

export default router;
