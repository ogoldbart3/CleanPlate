package com.olivergoldbart.cleanplate;

import org.json.JSONException;
import org.json.JSONObject;

import android.util.Log;

import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;


/**
 * Created by Oliver on 11/11/13.
 */
public class Restaurant {

    int restaurantID;
    String name, restaurantAddress, restaurantEmail, restaurantPhoneNumber;


    public Restaurant( int restaurantID, String name, String restaurantAddress, String restaurantEmail, String restaurantPhoneNumber ) {
        this.restaurantID = restaurantID;
        this.name = name;
        this.restaurantAddress = restaurantAddress;
        this.restaurantEmail = restaurantEmail;
        this.restaurantPhoneNumber = restaurantPhoneNumber;
    }
    
    public Restaurant() {
    	this(1, "", "", "", "");
    }

    public int getRestaurantID() {
        return restaurantID;
    }

    public String getRestaurantName() {
        return name;
    }

    public String getRestaurantAddress() {
        return restaurantAddress;
    }

    public String getRestaurantEmail() {
        return restaurantEmail;
    }

    public String getRestaurantPhoneNumber() {
        return restaurantPhoneNumber;
    }

    //setters

    public void setRestaurantID(int restaurantID) {
        this.restaurantID = restaurantID;
    }

    public void setRestaurantName( String name ) {
        this.name = name;
    }

    public void setRestaurantAddress(String restaurantAddress) {
        this.restaurantAddress = restaurantAddress;
    }

    public void setRestaurantEmail(String restaurantEmail) {
        this.restaurantEmail = restaurantEmail;
    }
   
    public void setRestaurantPhoneNumber(String restaurantPhoneNumber) {
        this.restaurantPhoneNumber = restaurantPhoneNumber;
    }


}
