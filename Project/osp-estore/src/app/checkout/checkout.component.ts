import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule, FormGroup, FormControl, Validators } from '@angular/forms';
import { RouterLink, Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { LoginService } from '../login.service';
import { CartService } from '../cart.service';

@Component({
  selector: 'app-checkout',
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule, RouterLink],
  templateUrl: './checkout.component.html',
  styleUrl: './checkout.component.css'
})
export class CheckoutComponent {
  router = inject(Router);
  http = inject(HttpClient);
  loginService = inject(LoginService);
  cartService = inject(CartService);
  
  loggedIn = false;
  cart: any[] = [];
  subtotal = 0;
  shipping = 15;
  total = 0;
  

  warehouseSelected = false;
  addressFilled = false;
  

  paymentMethods = ['credit', 'debit', 'gift'];
  selectedPaymentMethod = 'credit';
  

  // DUMMY INFO 
  dummyUserInfo = {
    name: 'John Doe',
    email: 'john.doe@example.com',
    address: '123 Main St',
    city: 'Toronto',
    province: 'ON',
    postal: 'M5V 2N4'
  };
  // DUMMY INFO
  
  checkoutForm = new FormGroup({

    warehouse: new FormControl('1', [Validators.required]),
    timeSlot: new FormControl('morning', [Validators.required]),
    deliveryType: new FormControl('regular', [Validators.required]),
    

    address: new FormControl('', [Validators.required]),
    city: new FormControl('', [Validators.required]),
    deliveryDate: new FormControl('', [Validators.required]),
    

    paymentMethod: new FormControl('credit', [Validators.required]),
    

    cardNumber: new FormControl('', [Validators.pattern(/^\d{16}$/)]),
    cardExpiry: new FormControl('', []),
    cardCvv: new FormControl('', [Validators.pattern(/^\d{3,4}$/)]),
    

    giftCardNumber: new FormControl('', [Validators.pattern(/^\d{16}$/)])
  });
  
  ngOnInit() {
/*
    if (this.loginService.getLoginState()) {
        this.loggedIn = true
        this.logged_name = this.loginService.getLoginName()
  
        this.updateCartDisplay()
      } else {
            this.router.navigate(['/signin'], {queryParams: {'from-cart': true}})
      }
    }
*/  
    // DUMMY LOGIN 
    this.loggedIn = true;
    // DUMMY LOGIN
    
    if (!this.loggedIn) {
      this.router.navigate(['/signin'], { queryParams: { 'from-checkout': true } });
    }
    

    this.checkoutForm.patchValue({
      address: this.dummyUserInfo.address,
      city: this.dummyUserInfo.city
    });
    

    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    
 
    const minDate = tomorrow.toISOString().split('T')[0];
    

    const deliveryDateInput = document.querySelector('#deliveryDate') as HTMLInputElement;
    if (deliveryDateInput) {
      deliveryDateInput.min = minDate;
    }
    

    this.loadCartItems();
    

    this.checkoutForm.get('warehouse')?.valueChanges.subscribe(() => {
      this.warehouseSelected = true;
      this.updateDeliveryMap();
    });
    
    this.checkoutForm.get('address')?.valueChanges.subscribe(() => {
      if (this.checkoutForm.get('address')?.valid && this.checkoutForm.get('city')?.valid) {
        this.addressFilled = true;
        this.updateDeliveryMap();
      }
    });
    
    this.checkoutForm.get('city')?.valueChanges.subscribe(() => {
      if (this.checkoutForm.get('address')?.valid && this.checkoutForm.get('city')?.valid) {
        this.addressFilled = true;
        this.updateDeliveryMap();
      }
    });
    

    this.checkoutForm.get('paymentMethod')?.valueChanges.subscribe(method => {
      this.selectedPaymentMethod = method || 'credit';
      

      this.checkoutForm.get('cardNumber')?.clearValidators();
      this.checkoutForm.get('cardExpiry')?.clearValidators();
      this.checkoutForm.get('cardCvv')?.clearValidators();
      this.checkoutForm.get('giftCardNumber')?.clearValidators();
      

      if (method === 'credit' || method === 'debit') {
        this.checkoutForm.get('cardNumber')?.setValidators([Validators.required, Validators.pattern(/^\d{16}$/)]);
        this.checkoutForm.get('cardExpiry')?.setValidators([Validators.required]);
        this.checkoutForm.get('cardCvv')?.setValidators([Validators.required, Validators.pattern(/^\d{3,4}$/)]);
      } else if (method === 'gift') {
        this.checkoutForm.get('giftCardNumber')?.setValidators([Validators.required, Validators.pattern(/^\d{16}$/)]);
      }
      

      this.checkoutForm.get('cardNumber')?.updateValueAndValidity();
      this.checkoutForm.get('cardExpiry')?.updateValueAndValidity();
      this.checkoutForm.get('cardCvv')?.updateValueAndValidity();
      this.checkoutForm.get('giftCardNumber')?.updateValueAndValidity();
    });
    

    this.checkoutForm.get('deliveryType')?.valueChanges.subscribe(type => {
      this.shipping = type === 'express' ? 25 : 15;
      this.calculateTotal();
      

      this.updateMinDeliveryDate(type);
    });
    

    this.updateDeliveryMap();
  }
  
  updateMinDeliveryDate(type: string | null) {
    const deliveryDateInput = document.querySelector('#deliveryDate') as HTMLInputElement;
    if (!deliveryDateInput) return;
    
    const today = new Date();
    let minDate;
    
    if (type === 'express') {

      const minExpressDay = new Date(today);
      minExpressDay.setDate(today.getDate() + 1);
      minDate = minExpressDay.toISOString().split('T')[0];
      

      const maxExpressDay = new Date(today);
      maxExpressDay.setDate(today.getDate() + 3);
      deliveryDateInput.max = maxExpressDay.toISOString().split('T')[0];
    } else {

      const minRegularDay = new Date(today);
      minRegularDay.setDate(today.getDate() + 4);
      minDate = minRegularDay.toISOString().split('T')[0];
      deliveryDateInput.removeAttribute('max');
    }
    
    deliveryDateInput.min = minDate;
    

    const currentDate = new Date(this.checkoutForm.get('deliveryDate')?.value || '');
    const minDateObj = new Date(minDate);
    
    if (currentDate < minDateObj) {
      this.checkoutForm.patchValue({ deliveryDate: minDate });
    }
  }
  
  loadCartItems() {

    let cartIds = localStorage.getItem('cart') || null;
    
    if (cartIds != null) {
      let cartIdsJson = JSON.parse(cartIds);
      
      // DUMMY DATA START
      this.cart = [
        { item_id: 1, item_name: 'Nintendo Switch', price: 399.99, quantity: 1 },
        { item_id: 2, item_name: 'Nintendo Switch Pro Controller', price: 89.99, quantity: 2 }
      ];
      this.calculateTotal();
      // DUMMYDATA
      
      // uncomment later
      /*
      this.http.post('http://localhost/CPS630/Project/backend/php/get-cart-data.php', cartIdsJson, {
        observe: 'response', 
        responseType: 'json'
      }).subscribe(res => {
        this.cart = JSON.parse(JSON.stringify(res.body));
        this.calculateTotal();
      });
      */
    } else {

      this.router.navigate(['/cart']);
    }
  }
  
  calculateTotal() {
    this.subtotal = this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    this.total = this.subtotal + this.shipping;
  }
  
  updateDeliveryMap() {
    if (!this.warehouseSelected || !this.addressFilled) return;
    
    const warehouseId = this.checkoutForm.get('warehouse')?.value;
    const address = this.checkoutForm.get('address')?.value;
    const city = this.checkoutForm.get('city')?.value;
    
    if (!warehouseId || !address || !city) return;
    
    const warehouseLocations: {[key: string]: string} = {
      '1': '1507 Yonge St, Toronto, ON',
      '2': '25 Queens Quay E, Toronto, ON',
      '3': '1000 Murray Ross Pkwy, North York, ON'
    };
    
    const warehouseAddress = warehouseLocations[warehouseId];
    const destinationAddress = `${address}, ${city}`;
    
    if (warehouseAddress && destinationAddress.length > 5) {
      const originEncoded = encodeURIComponent(warehouseAddress);
      const destinationEncoded = encodeURIComponent(destinationAddress);
      
      const mapContainer = document.getElementById('map-frame-container');
      if (mapContainer) {
        mapContainer.innerHTML = `
          <iframe 
            class="map-frame"
            frameborder="0" 
            style="border:0;width:100%;height:300px;"
            src="https://www.google.com/maps/embed/v1/directions?origin=${originEncoded}&destination=${destinationEncoded}&key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8" 
            allowfullscreen>
          </iframe>
        `;
      }
    }
  }
  
  onSubmit() {
    if (!this.checkoutForm.valid) {

      Object.keys(this.checkoutForm.controls).forEach(key => {
        const control = this.checkoutForm.get(key);
        control?.markAsTouched();
      });
      return;
    }
    

    const formValues = this.checkoutForm.value;
    

    const orderData = {
      items: this.cart,
      subtotal: this.subtotal,
      shipping: this.shipping,
      total: this.total,
      warehouse: formValues.warehouse,
      timeSlot: formValues.timeSlot,
      deliveryType: formValues.deliveryType,
      address: formValues.address,
      city: formValues.city,
      deliveryDate: formValues.deliveryDate,
      paymentMethod: formValues.paymentMethod,
      paymentDetails: formValues.paymentMethod === 'gift' 
        ? { giftCardNumber: formValues.giftCardNumber }
        : { 
            cardNumber: formValues.cardNumber,
            cardExpiry: formValues.cardExpiry,
            cardCvv: formValues.cardCvv
          }
    };
    
    // Uncomment later?
    /*
    this.http.post('http://localhost/CPS630/Project/backend/php/process-checkout.php', orderData, {
      observe: 'response',
      responseType: 'json'
    }).subscribe(res => {
      const response = JSON.parse(JSON.stringify(res.body));
      
      if (response.success) {
        // Save order info to service for order confirmation page
        this.cartService.setOrderInfo(response.orderInfo);
        
        // Clear cart
        localStorage.removeItem('cart');
        
        // Redirect to order confirmation
        this.router.navigate(['/order-confirmation'], { queryParams: { order_id: response.orderId } });
      } else {
        // Handle error
        console.error('Error processing order:', response.error);
      }
    });
    */
    
    // DUMMY PROCESSING
    console.log('Order data:', orderData);
    
    // dummy order info
    const dummyOrderInfo = {
      order_id: Math.floor(Math.random() * 10000),
      order_date: new Date().toISOString(),
      delivery_date: formValues.deliveryDate,
      total_price: this.total,
      customer_name: this.dummyUserInfo.name,
      customer_address: `${formValues.address}, ${formValues.city}`,
      warehouse: formValues.warehouse === '1' ? 'Downtown Warehouse' : 
                 formValues.warehouse === '2' ? 'South Branch' : 'North Branch',
      warehouse_address: formValues.warehouse === '1' ? '1507 Yonge St, Toronto, ON' : 
                          formValues.warehouse === '2' ? '25 Queens Quay E, Toronto, ON' : 
                          '1000 Murray Ross Pkwy, North York, ON',
      truck_id: 'TRK' + Math.floor(Math.random() * 100),
      items: this.cart,
      subtotal: this.subtotal,
      shipping: this.shipping,
      deliveryType: formValues.deliveryType
    };
    

    this.cartService.setOrderInfo(dummyOrderInfo);
    

    localStorage.removeItem('cart');
    

    this.router.navigate(['/order-confirmation'], { queryParams: { order_id: dummyOrderInfo.order_id } });
  }
}