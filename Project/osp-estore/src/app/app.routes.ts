import { Routes } from '@angular/router';
import { HomeComponent } from './home/home.component'
import { AboutComponent } from "./about/about.component";
import { ServicesComponent } from './services/services.component';
import { CartComponent } from './cart/cart.component';
import { SigninComponent } from './signin/signin.component';
import { SignupComponent } from './signup/signup.component';

export const routes: Routes = [
    {
        path: '',
        title: 'Home - OSP Electronics',
        component: HomeComponent
    },
    {
        path: 'about',
        title: 'About - OSP Electronics',
        component: AboutComponent
    },
    {
        path: 'services',
        title: 'Services - OSP Electronics',
        component: ServicesComponent
    },
    // {
    //     path: 'db-maintain/insert',
    //     title: 'Database Maintain - Insert - OSP Electronics',
    //     component:
    // },
    {
        path: 'cart',
        title: 'Cart - OSP Electronics',
        component: CartComponent
    },
    {
        path: 'signin',
        title: 'Sign In - OSP Electronics',
        component: SigninComponent
    },
    {
        path: 'signup',
        title: 'Sign Up - OSP Electronics',
        component: SignupComponent
    },
];
