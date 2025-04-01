import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { FormGroup, FormControl, Validators, ReactiveFormsModule } from '@angular/forms';
import { CartService } from '../cart.service';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-order-confirmation',
  standalone: true,
  imports: [CommonModule, RouterLink, ReactiveFormsModule],
  templateUrl: './order-confirmation.component.html',
  styleUrl: './order-confirmation.component.css'
})
export class OrderConfirmationComponent {
  route = inject(ActivatedRoute);
  router = inject(Router);
  cartService = inject(CartService);
  http = inject(HttpClient);
  
  orderId?: number;
  orderInfo: any = null;
  

  reviewForm = new FormGroup({
    rating: new FormControl('5', [Validators.required]),
    review: new FormControl('', [Validators.required, Validators.maxLength(100)])
  });
  
  reviewSubmitted = false;
  selectedItemId: number | null = null;
  
  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      this.orderId = params['order_id'];
      
      if (!this.orderId) {

        this.router.navigate(['/']);
        return;
      }
      

      this.orderInfo = this.cartService.getOrderInfo();
      
      if (!this.orderInfo) {

        this.fetchOrderInfo();
      }
    });
  }
  
  fetchOrderInfo() {
    // uncomment later
    /*
    this.http.get(`http://localhost/CPS630/Project/backend/php/get-order.php?order_id=${this.orderId}`, {
      observe: 'response',
      responseType: 'json'
    }).subscribe(res => {
      const response = JSON.parse(JSON.stringify(res.body));
      
      if (response.success) {
        this.orderInfo = response.orderInfo;
      } else {
        // Handle error
        console.error('Error fetching order info:', response.error);
        this.router.navigate(['/']);
      }
    });
    */
    
    //DUMMY
    this.orderInfo = {
      order_id: this.orderId,
      order_date: new Date().toISOString(),
      delivery_date: new Date(new Date().getTime() + 7 * 24 * 60 * 60 * 1000).toISOString(),
      total_price: 579.97,
      customer_name: 'John Doe',
      customer_address: '123 Main St, Toronto, ON',
      warehouse: 'Downtown Warehouse',
      warehouse_address: '1507 Yonge St, Toronto, ON',
      truck_id: 'TRK45',
      items: [
        { item_id: 1, item_name: 'Nintendo Switch', price: 399.99, quantity: 1 },
        { item_id: 2, item_name: 'Nintendo Switch Pro Controller', price: 89.99, quantity: 2 }
      ],
      subtotal: 579.97,
      shipping: 15.00,
      deliveryType: 'regular'
    };
  }
  
  openReviewModal(itemId: number) {
    this.selectedItemId = itemId;
    this.reviewForm.reset({
      rating: '5',
      review: ''
    });
    this.reviewSubmitted = false;
  }
  
  submitReview() {
    if (!this.reviewForm.valid || !this.selectedItemId) return;
    
    const reviewData = {
      order_id: this.orderId,
      item_id: this.selectedItemId,
      rating: this.reviewForm.value.rating,
      review: this.reviewForm.value.review
    };
    
    // uncomment later
    /*
    this.http.post('http://localhost/CPS630/Project/backend/php/submit-review.php', reviewData, {
      observe: 'response',
      responseType: 'json'
    }).subscribe(res => {
      const response = JSON.parse(JSON.stringify(res.body));
      
      if (response.success) {
        this.reviewSubmitted = true;
        // Update the item in orderInfo to show it's been reviewed
        this.orderInfo.items = this.orderInfo.items.map((item: any) => {
          if (item.item_id === this.selectedItemId) {
            return { ...item, reviewed: true };
          }
          return item;
        });
      } else {
        // Handle error
        console.error('Error submitting review:', response.error);
      }
    });
    */
    
    // DUMMY data
    console.log('Review data:', reviewData);
    this.reviewSubmitted = true;
    

    this.orderInfo.items = this.orderInfo.items.map((item: any) => {
      if (item.item_id === this.selectedItemId) {
        return { ...item, reviewed: true };
      }
      return item;
    });
  }}