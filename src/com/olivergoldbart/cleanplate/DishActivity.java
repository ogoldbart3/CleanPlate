package com.olivergoldbart.cleanplate;

import java.text.DecimalFormat;
import java.util.ArrayList;
import java.util.Collection;
import java.util.LinkedList;
import java.util.Queue;
import java.util.Stack;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.olivergoldbart.cleanplate.R;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.os.Parcelable;
import android.util.Log;
import android.view.View;
import android.widget.TextView;

public class DishActivity extends Activity {

	//String storing the dish_id of the currently displaying dish
	String currentRestaurantID;
	String currentFoodmenuID;
	String currentDishID;
	
	String url = "http://m3.cip.gatech.edu/d/ogoldbart3/w/cleanplate/c/api/";
	String urlAddon;
    
	
	//ArrayList acting as the history for the app
	//New dishes are added to the head of the arrayList
	//While it is technically all charsqeuences, you can
	//just add strings to it
	ArrayList<CharSequence> arrayList;
	
	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {
	    
		super.onCreate(savedInstanceState);
	    setContentView(R.layout.activity_dish);
        
        Intent oldIntent = getIntent();
        
        arrayList = oldIntent.getCharSequenceArrayListExtra("arrayList");        
        Log.v("testcat", "testcat " + arrayList.toString());
        
    	currentRestaurantID = oldIntent.getExtras().getString("currentRestaurantID");
    	currentFoodmenuID = oldIntent.getExtras().getString("currentFoodmenuID");
    	currentDishID = oldIntent.getExtras().getString("currentDishID");
    	
        urlAddon = "dish/" + currentDishID;
        
        
		AsyncHttpClient client = new AsyncHttpClient();
		
		client.get( url + urlAddon, new AsyncHttpResponseHandler() {
			
			
			@Override
			public void onSuccess(String response) {
				try {
					JSONObject jsonRestaurant = new JSONObject(response);
					
				    TextView dishID = (TextView)findViewById(R.id.dishID);
				    TextView dishName = (TextView)findViewById(R.id.dishName);
				    TextView dishOrder = (TextView)findViewById(R.id.dishOrder);
				    TextView dishDescription = (TextView)findViewById(R.id.dishDescription);
				    TextView dishImageURL = (TextView)findViewById(R.id.dishImageURL);	
					TextView dishPrice = (TextView)findViewById(R.id.dishPrice);
					
					try {
					    dishID.setText(jsonRestaurant.getString("dish_id"));
					} catch (JSONException e) {
						dishID.setText("Failed");
					}
					
					try {
					    dishName.setText(jsonRestaurant.getString("dish_name"));
					} catch (JSONException e) {
					    dishName.setText("Failed");
					}
					
					try {
					    dishOrder.setText(jsonRestaurant.getString("dish_order"));
					} catch (JSONException e) {
					    dishOrder.setText("Failed");
					}
				
					try {
					    dishDescription.setText(jsonRestaurant.getString("dish_description"));
					} catch (JSONException e) {
					    dishDescription.setText("Failed");
					}
					
					try {
					    dishImageURL.setText(jsonRestaurant.getString("dish_image_URL"));
					} catch (JSONException e) {
					    dishImageURL.setText("Failed");
					}
					
					try {
						DecimalFormat df = new DecimalFormat("0.00");
						
					    dishPrice.setText("$" + df.format(jsonRestaurant.getDouble("dish_price")));
					} catch (JSONException e) {
					    dishPrice.setText("Failed");
					}
					
				} catch (JSONException e) {
					Log.v("testcat", "testcat failed");
				}	
			}
		});
		
	}
	
	public void randomDish( View view ) {
		
		urlAddon = "restaurant/" + currentRestaurantID + "/randomOtherDish/";
		
		AsyncHttpClient client = new AsyncHttpClient();
		
		client.get( url + urlAddon, new AsyncHttpResponseHandler() {
			
			@Override
			public void onSuccess(String response) {
				try {
					JSONObject jsonDish = new JSONObject(response);
					
					try {
						currentRestaurantID = jsonDish.getString("restaurant_id");
						currentFoodmenuID = jsonDish.getString("foodmenu_id");
 						currentDishID = jsonDish.getString("dish_id");
 						
 						Intent intent = new Intent();
 						
 						arrayList.add(0, currentDishID);
 						intent.putCharSequenceArrayListExtra("arrayList", arrayList);
 						
 						intent.putExtra( "currentRestaurantID", currentRestaurantID );
 						intent.putExtra( "currentFoodmenuID", currentFoodmenuID );
 						intent.putExtra( "currentDishID", currentDishID );
 						
 						intent.setClass(DishActivity.this, DishActivity.class);

 						startActivity(intent);
 						finish();
					} catch (JSONException e) {
						Log.v("testcat", "testcat failed pulling ID");
					}
					
				} catch (JSONException e) {
					Log.v("testcat", "testcat failed");
				}	
			}
		});
	}
	
	public void aboveDishSameMenu( View view ) {
		
		urlAddon = "foodmenu/" + currentFoodmenuID + "/dish/" + currentDishID + "/prev";
		
		AsyncHttpClient client = new AsyncHttpClient();
		
		client.get( url + urlAddon, new AsyncHttpResponseHandler() {
			
			@Override
			public void onSuccess(String response) {
				try {
					JSONObject jsonDish = new JSONObject(response);
					
					try {
						currentDishID = jsonDish.getString("dish_id");
 						
 						Intent intent = new Intent();

 						arrayList.remove(0);
 						arrayList.add(0,currentDishID);
 						intent.putCharSequenceArrayListExtra("arrayList", arrayList);
 						
 						intent.putExtra( "currentRestaurantID", currentRestaurantID );
 						intent.putExtra( "currentFoodmenuID", currentFoodmenuID );
 						intent.putExtra( "currentDishID", currentDishID );
 						
 						intent.setClass(DishActivity.this, DishActivity.class);

 						startActivity(intent);
 						finish();
					} catch (JSONException e) {
						Log.v("testcat", "testcat failed pulling ID");
					}
					
				} catch (JSONException e) {
					Log.v("testcat", "testcat failed");
				}	
			}
		});
	}
	
	public void belowDishSameMenu( View view ) {
		
		urlAddon = "foodmenu/" + currentFoodmenuID + "/dish/" + currentDishID + "/next";
		
		AsyncHttpClient client = new AsyncHttpClient();
		
		client.get( url + urlAddon, new AsyncHttpResponseHandler() {
			
			@Override
			public void onSuccess(String response) {
				try {
					JSONObject jsonDish = new JSONObject(response);
					
					try {
						currentDishID = jsonDish.getString("dish_id");
 						
 						Intent intent = new Intent();

 						arrayList.remove(0);
 						arrayList.add(0,currentDishID);
 						intent.putCharSequenceArrayListExtra("arrayList", arrayList);
 						
 						intent.putExtra( "currentRestaurantID", currentRestaurantID );
 						intent.putExtra( "currentFoodmenuID", currentFoodmenuID );
 						intent.putExtra( "currentDishID", currentDishID );
 						
 						intent.setClass(DishActivity.this, DishActivity.class);

 						startActivity(intent);
 						finish();
					} catch (JSONException e) {
						Log.v("testcat", "testcat failed pulling ID");
					}
					
				} catch (JSONException e) {
					Log.v("testcat", "testcat failed");
				}	
			}
		});
	}
	
	public void previousDish( View view ) {
		
		if ( arrayList.size() == 1 ) {
			Intent intent = new Intent();
			
			intent.setClass(DishActivity.this, MainActivity.class);
	
			startActivity(intent);
			finish();
			
		} else {
		
			Intent intent = new Intent();

			arrayList.remove(0);
			currentDishID = (String) arrayList.get(0);
				
			intent.putExtra( "currentDishID", currentDishID );
			intent.putCharSequenceArrayListExtra("arrayList", arrayList);
			
			intent.setClass(DishActivity.this, DishActivity.class);
		
			startActivity(intent);
			finish();
		}
	}
}
 