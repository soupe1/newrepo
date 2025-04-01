import { Component, inject } from '@angular/core';
import { CookieService } from 'ngx-cookie-service';
import { Router, RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';
import { LoginService } from '../login.service';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './cart.component.html',
  styleUrl: './cart.component.css'
})
export class CartComponent {
  cookieService = inject(CookieService);
  loginService = inject(LoginService);
  router = inject(Router);
  http = inject(HttpClient);
  
  loggedIn = false;
  logged_name = '';
  
  cart: any[] = [];
  subtotal = 0;
  shipping = 15;
  total = 0;

  ngOnInit() {
    // DUMMY LOGIN
    this.loggedIn = true;
    this.logged_name = 'John Doe';
    // DUMMY LOGIN
    
    
    this.updateCartDisplay();
  }

  updateCartDisplay() {

    let cart = localStorage.getItem('cart') || null;

    if (cart != null) {
      let cart_json = JSON.parse(cart);
      this.http.post('http://localhost/CPS630/Project/backend/php/get-cart-data.php', cart_json, {
        observe: 'response',
        responseType: 'json'
      }).subscribe(res => {
        this.cart = JSON.parse(JSON.stringify(res.body));
        this.calculateTotal();
      });
    } else {
      this.cart = [];
      this.calculateTotal();
    }
  }
  
  calculateTotal() {
    this.subtotal = this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    this.total = this.subtotal + this.shipping;
  }

  removeItem(event: MouseEvent) {
    let cart = localStorage.getItem('cart') || null;

    if (cart != null) {
      let cart_json = JSON.parse(cart);
      let target = event.target as HTMLElement;
      let removed_item = target.getAttribute('data-value');

      if (removed_item) {
        cart_json = cart_json.filter((id: string) => id !== removed_item);
        localStorage.setItem('cart', JSON.stringify(cart_json));
        this.updateCartDisplay();
      }
    }
  }

  updateQuantity(event: Event) {
    const newQuantity = parseInt((event.target as HTMLInputElement).value);
    const itemId = (event.target as HTMLInputElement).id;
    let cart = localStorage.getItem('cart') || null;

    if (cart != null) {
      let cart_json = JSON.parse(cart);
     
      let count = cart_json.filter((id: string) => id === itemId).length;

      if (newQuantity > count) {

        for (let i = 0; i < (newQuantity - count); i++) {
          cart_json.push(itemId);
        }
      } else if (newQuantity < count) {
        cart_json = cart_json.reverse()

        let removed = 0;
        cart_json = cart_json.filter((id: string) => {
          if (id === itemId && removed < (count - newQuantity)) {
            removed++;
            return false;
          }
          return true;
        });
        cart_json = cart_json.reverse()
      }

      localStorage.setItem('cart', JSON.stringify(cart_json));
      this.updateCartDisplay();
    }
  }
  
  proceedToCheckout() {
    if (this.cart.length > 0) {
      this.router.navigate(['/checkout']);
    }
  }
}
