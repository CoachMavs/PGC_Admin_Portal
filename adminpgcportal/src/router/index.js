/* eslint-disable */
import axios from 'axios';
import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'Login',
    meta: {
      auth: 0
    },
    component: () => import('../views/LoginForm.vue'),
  },
  {
    path: '/MyToolTip',
    name: 'MyToolTip',
    meta: {
      auth: 0
    },
    component: () => import('../views/MyToolTip.vue'),
  },
  {
    path: '/home',
    component: () => import('../layouts/layoutauthenticated.vue'),
    children: [
      {
        path: '/',
        name: 'home',
        meta: {
          auth: 1
        },
        component: HomeView

      },
      {
        path: '/Dashboard',
        name: 'Dashboard',
        meta: {
          auth: 1
        },
        component: () => import('../views/Dashboard.vue')
      },

      // PGC Portal
      {
        path: '/Employees/',
        name: 'Employees',
        meta: {
          auth: 1
        },
        component: () => import('../views/Employees.vue')
      },

      {
        path: '/Directories/',
        name: 'Directories',
        meta: {
          auth: 1
        },
        component: () => import('../views/Directories.vue')
      },

      {
        path: '/ZoomRequest/',
        name: 'ZoomRequest',
        meta: {
          auth: 1
        },
        component: () => import('../views/ZoomRequest.vue')
      },

      {
        path: '/ZoomUpcoming/',
        name: 'ZoomUpcoming',
        meta: {
          auth: 1
        },
        component: () => import('../views/ZoomUpcoming.vue')
      },

      {
        path: '/ZoomPrevious/',
        name: 'ZoomPrevious',
        meta: {
          auth: 1
        },
        component: () => import('../views/ZoomPrevious.vue')
      },

      {
        path: '/RepairsRequest/',
        name: 'RepairsRequest',
        meta: {
          auth: 1
        },
        component: () => import('../views/RepairsRequest.vue')
      },

      {
        path: '/RepairsForReceiving/',
        name: 'RepairsForReceiving',
        meta: {
          auth: 1
        },
        component: () => import('../views/RepairsForReceiving.vue')
      },

      {
        path: '/RepairsCurrent/',
        name: 'RepairsCurrent',
        meta: {
          auth: 1
        },
        component: () => import('../views/RepairsCurrent.vue')
      },

      {
        path: '/RepairsPrevious/',
        name: 'RepairsPrevious',
        meta: {
          auth: 1
        },
        component: () => import('../views/RepairsPrevious.vue')
      },

      {
        path: '/CertPre/',
        name: 'CertPre',
        meta: {
          auth: 1
        },
        component: () => import('../views/CertPre.vue')
      },

      {
        path: '/CertPost/',
        name: 'CertPost',
        meta: {
          auth: 1
        },
        component: () => import('../views/CertPost.vue')
      },

      {
        path: '/CertWaste/',
        name: 'CertWaste',
        meta: {
          auth: 1
        },
        component: () => import('../views/CertWaste.vue')
      },

        {
        path: '/ExportToExcelRepairs/',
        name: 'ExportToExcelRepairs',
        meta: {
          auth: 1
        },
        component: () => import('../views/ExportToExcelRepairs.vue')
      },

           {
        path: '/ExportToExcelZoom/',
        name: 'ExportToExcelZoom',
        meta: {
          auth: 1
        },
        component: () => import('../views/ExportToExcelZoom.vue')
      },
      
      {
        path: '/GoToMono',
        name: 'GoToMono',
         meta: {
          auth: 1
        },
        component: () => import('../views/GoToMono.vue')
      },
      
      {
        path: '/ChangeStatusDialog',
        name: 'ChangeStatusDialog',
         meta: {
          auth: 1
        },
        component: () => import('../views/Modal/ChangeStatusDialog.vue')
      }


    ]
  },


]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

let checkAuthentication = () => {
  return new Promise((resolve, reject) => {
    axios({
      method: 'post',
      url: process.env.VUE_APP_API + 'auth/me',
      headers: {
        Authorization: 'Bearer ' + localStorage.getItem('xxx')
      }
    }).then((resp) => {
      localStorage.setItem('id', resp.data.id);
      localStorage.setItem('DN', resp.data.empISU);
      resolve(resp)
    }).catch((err) => {
      console.log(err)
      reject(err)
    })
  })
}
router.beforeEach((to, from, next) => {

  if (localStorage.getItem('xxx')) {

    checkAuthentication()
      .then((resp) => {
        if (to.meta.auth == 1) {
          next()
        } else {
          next({ name: 'Dashboard' })
        }
      }).catch((err) => {
        localStorage.removeItem('id');
        localStorage.removeItem('DN');
        localStorage.removeItem('xxx')
        next({ name: 'Login' });
      })

  } else {
    if (to.name == 'Login') {
      next()
    } else {
      next({ name: 'Login' });
    }
  }

  //next()

})

export default router
