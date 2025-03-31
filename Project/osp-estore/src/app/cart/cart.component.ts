import { Component, inject } from '@angular/core';
import { CookieService } from 'ngx-cookie-service';
import { Router, RouterLink } from '@angular/router';
import { LoginService } from '../login.service';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-cart',
  imports: [RouterLink],
  templateUrl: './cart.component.html',
  styleUrl: './cart.component.css'
})
export class CartComponent {
  cookieService = inject(CookieService)
  loginService = inject(LoginService)
  router = inject(Router)
  http = inject(HttpClient)
  loggedIn = false
  logged_name = ''

  cart: any = []

  ngOnInit() {
    if (this.loginService.getLoginState()) {
      this.loggedIn = true
      this.logged_name = this.loginService.getLoginName()

      this.updateCartDisplay()
    } else {
		  this.router.navigate(['/signin'], {queryParams: {'from-cart': true}})
    }
  }

  updateCartDisplay() {
    let cart = localStorage.getItem('cart') || null;

    if (cart != null) {
      let cart_json = JSON.parse(cart)

      this.http.post('http://localhost/CPS630/Project/backend/php/get-cart-data.php', cart_json, {observe: 'response', responseType: 'json'}).subscribe(res => {
        this.cart = JSON.parse(JSON.stringify(res.body))
      })
    }
  }

  removeItem(event: MouseEvent) {
    let cart = localStorage.getItem('cart') || null;

    if (cart != null) {
      let cart_json = JSON.parse(cart)
      let removed_item = (event.target as HTMLElement).getAttribute('value')

      cart_json = cart_json.reverse()
      cart_json.splice(cart_json.indexOf(removed_item), 1)

      cart_json = cart_json.reverse()
      localStorage.setItem('cart', JSON.stringify(cart_json))
      this.updateCartDisplay()
    }
  }

  updateQuantity(event: Event) {
    const newQuantity = parseInt((event.target as HTMLInputElement).value)
    const itemId = (event.target as HTMLInputElement).id
    let cart = localStorage.getItem('cart') || null;

    if (cart != null) {
      let cart_json = JSON.parse(cart)
     
      let count = 0
      cart_json.forEach((element: any) => {
        if (element == itemId) {
          count++;
        }
      })

      if (newQuantity > count) {
        for (let i = 0; i < (newQuantity - count); i++) {
          cart_json.push(itemId);
        }
      } else if (newQuantity < count) {
        for (let i = 0; i < (count - newQuantity); i++) {          
          cart_json.splice(cart_json.indexOf(itemId), 1);
        }
      }

      localStorage.setItem('cart', JSON.stringify(cart_json))

      this.updateCartDisplay()
    }
  }
}
