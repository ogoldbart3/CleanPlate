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
	String currentDishID;
	
	//ArrayList acting as the history for the app
	//New dishes are added to the head of the arrayList
	//While it is technically all charsqeuences, you can
	//just add strings to it
	ArrayList<CharSequence> arrayList;
	
	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {
	    
		//Takes in data to go directly into a saved point
		//from minimization
		super.onCreate(savedInstanceState);
		
		//Reads in the XML file for the activity layout
        setContentView(R.layout.activity_dish);
        
        //Manually makes the app slide in from right
        overridePendingTransition(R.anim.anim_in,R.anim.anim_out);
		
        //Intents are like bundles containing app data
        //that can be passed between activities
        //Here we are pulling the intent passed into the
        //current activity we are in, so we can get the 
        //data the last activity is giving to us
        Intent oldIntent = getIntent();
        
        
        //here we are pulling data using getExtras() and
        //setting their current variables in this activity
        //to what the old activity had "saved" them as
        String oldDishID = (String) oldIntent.getStringExtra("dishID");
        Boolean sameMenuCheck = oldIntent.getExtras().getBoolean("sameMenu");
		arrayList = oldIntent.getCharSequenceArrayListExtra("arrayList");
		
		Log.v("testcat", "testcat " + oldDishID + ", " + arrayList.toString());
        
        String url = "http://m3.cip.gatech.edu/d/ogoldbart3/w/cleanplate/c/api/dish/" + oldDishID + "/randomOther";
        
        if ( sameMenuCheck ) {
        	url += "SameMenu";
        }
		AsyncHttpClient client = new AsyncHttpClient();
		
		client.get( url, new AsyncHttpResponseHandler() {
			
			
			@Override
			public void onSuccess(String response) {
				try {
					JSONObject jsonRestaurant = new JSONObject(response);
				    TextView dishID = (TextView)findViewById(R.id.dishID);
				    TextView dishName = (TextView)findViewById(R.id.dishName);
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
		
        
	    // TODO Auto-generated method stub
	}
	
	public void randomDish( View view ) {
		
		TextView dishID = (TextView)findViewById(R.id.dishID);
	    currentDishID = dishID.getText().toString();

	    arrayList.add(0, currentDishID);
	    
	    Intent intent = new Intent();

		intent.putExtra("dishID", currentDishID);
		intent.putExtra("sameMenu", false);
		intent.putCharSequenceArrayListExtra("arrayList", arrayList);
		
		intent.setClass(DishActivity.this, DishActivity.class);
		

		startActivity(intent);
		finish();

	}
	
public void randomDishSameMenu( View view ) {
		
		TextView dishID = (TextView)findViewById(R.id.dishID);
	    currentDishID = dishID.getText().toString();

	    arrayList.add(0, currentDishID);
		
		Intent intent = new Intent();

		intent.putExtra("dishID", currentDishID);
		intent.putExtra("sameMenu", true);
		intent.putCharSequenceArrayListExtra("arrayList", arrayList);
		
		intent.setClass(DishActivity.this, DishActivity.class);

		startActivity(intent);
		finish();

	}
}
 