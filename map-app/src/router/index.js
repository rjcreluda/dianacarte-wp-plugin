import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'
import About from '../views/About.vue'
import CreatePackage from '../views/CreatePackage.vue'
import MyPackages from '../views/MyPackages.vue'
import SinglePackage from '../views/SinglePackage.vue'
import EditPackage from '../views/EditPackage.vue'
import Commandes from '../views/Commandes.vue'
import SingleCommande from '../views/SingleCommande.vue'

Vue.use(VueRouter)

const routes = [
  // {
  //   // route level code-splitting
  //   // this generates a separate chunk (about.[hash].js) for this route
  //   // which is lazy-loaded when the route is visited.
  //   component: () => import(/* webpackChunkName: "about" */ '../views/About.vue')
  // }
/*  {
    path: '/test',
    name: 'Home',
    component: Home
  },*/
  {
    path: '/',
    name: 'Map',
    component: About
  },
  {
    path: '/mes-packages',
    name: 'MyPackages',
    component: MyPackages
  },
  {
    path: '/mes-packages/:id',
    name: 'SinglePackage',
    component: SinglePackage
  },
  {
    path: '/packages/:id/edit',
    name: 'EditPackage',
    component: EditPackage
  },
  {
    path: '/create-package',
    name: 'CreatePackage',
    component: CreatePackage
  },
  {
    path: '/commandes',
    name: 'Commandes',
    component: Commandes
  },
  {
    path: '/commandes/:id',
    name: 'SingleCommande',
    component: SingleCommande
  },
]

const router = new VueRouter({
  routes
})

export default router
