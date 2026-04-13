Use this gradle for the auth+calender/alarm if reqd:


plugins {
    alias(libs.plugins.android.application)
}

android {
    namespace = "com.example.myapplication"
    compileSdk = 34

    defaultConfig {
        applicationId = "com.example.myapplication"
        minSdk = 24
        targetSdk = 34
        versionCode = 1
        versionName = "1.0"

        testInstrumentationRunner = "androidx.test.runner.AndroidJUnitRunner"
    }

    buildTypes {
        release {
            isMinifyEnabled = false
            proguardFiles(
                getDefaultProguardFile("proguard-android-optimize.txt"),
                "proguard-rules.pro"
            )
        }
    }

    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_1_8
        targetCompatibility = JavaVersion.VERSION_1_8
    }
}

dependencies {
    implementation("androidx.appcompat:appcompat:1.6.1")
    implementation("com.google.android.material:material:1.11.0")
    implementation("androidx.constraintlayout:constraintlayout:2.1.4")
    implementation("androidx.core:core:1.12.0")
    testImplementation(libs.junit)
    androidTestImplementation(libs.ext.junit)
    androidTestImplementation(libs.espresso.core)
}









ADDITIONAL:

If you want to show a list of your saved events(sorted by deadline):

The files you'd need to change or add are:

CalendarActivity.java — add save-to-storage logic and show the list
activity_calendar.xml — add a ListView/RecyclerView below the form
A new file EventAdapter.java — to power the list display

That's it.



activity_calendar.xml — replace entirely:



<?xml version="1.0" encoding="utf-8"?>
<LinearLayout xmlns:android="http://schemas.android.com/apk/res/android"
    android:layout_width="match_parent"
    android:layout_height="match_parent"
    android:orientation="vertical"
    android:padding="16dp">

    <TextView
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:text="Calendar Events"
        android:textSize="22sp"
        android:textStyle="bold"
        android:layout_marginBottom="16dp" />

    <CalendarView
        android:id="@+id/calendarView"
        android:layout_width="match_parent"
        android:layout_height="wrap_content" />

    <com.google.android.material.textfield.TextInputLayout
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:hint="Event Title"
        style="@style/Widget.MaterialComponents.TextInputLayout.OutlinedBox"
        android:layout_marginTop="16dp">
        <com.google.android.material.textfield.TextInputEditText
            android:id="@+id/etEventTitle"
            android:layout_width="match_parent"
            android:layout_height="wrap_content" />
    </com.google.android.material.textfield.TextInputLayout>

    <Button
        android:id="@+id/btnPickTime"
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:text="Pick Event Time"
        android:layout_marginTop="12dp" />

    <TextView
        android:id="@+id/tvSelectedTime"
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:text="No time selected"
        android:layout_marginTop="8dp" />

    <Button
        android:id="@+id/btnAddEvent"
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:text="Add Event &amp; Schedule Notification"
        android:layout_marginTop="12dp" />

    <TextView
        android:layout_width="wrap_content"
        android:layout_height="wrap_content"
        android:text="Upcoming Events"
        android:textSize="18sp"
        android:textStyle="bold"
        android:layout_marginTop="20dp"
        android:layout_marginBottom="8dp" />

    <ListView
        android:id="@+id/lvEvents"
        android:layout_width="match_parent"
        android:layout_height="0dp"
        android:layout_weight="1" />

</LinearLayout>






EventAdapter.java — create this new file:


package com.example.myapp;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.Locale;

public class EventAdapter extends ArrayAdapter<long[]> {

    // Each long[] has two elements: [0] = triggerMillis, [1] = unused padding
    // We store title separately in a parallel list
    private final List<String> titles;
    private final List<Long> times;

    public EventAdapter(Context context, List<String> titles, List<Long> times) {
        super(context, android.R.layout.simple_list_item_2);
        this.titles = titles;
        this.times = times;
    }

    @Override
    public int getCount() {
        return titles.size();
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        if (convertView == null) {
            convertView = LayoutInflater.from(getContext())
                    .inflate(android.R.layout.simple_list_item_2, parent, false);
        }

        TextView text1 = convertView.findViewById(android.R.id.text1);
        TextView text2 = convertView.findViewById(android.R.id.text2);

        text1.setText(titles.get(position));

        SimpleDateFormat sdf = new SimpleDateFormat("dd MMM yyyy  HH:mm", Locale.getDefault());
        text2.setText(sdf.format(new Date(times.get(position))));

        return convertView;
    }
}





CalendarActivity.java — replace entirely:

package com.example.myapp;

import android.app.AlarmManager;
import android.app.PendingIntent;
import android.app.TimePickerDialog;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Build;
import android.os.Bundle;
import android.widget.Button;
import android.widget.CalendarView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import com.google.android.material.textfield.TextInputEditText;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collections;
import java.util.List;
import java.util.Map;

public class CalendarActivity extends AppCompatActivity {

    CalendarView calendarView;
    TextInputEditText etEventTitle;
    Button btnPickTime, btnAddEvent;
    TextView tvSelectedTime;
    ListView lvEvents;

    long selectedDateMillis = System.currentTimeMillis();
    int selectedHour = -1, selectedMinute = -1;

    List<String> eventTitles = new ArrayList<>();
    List<Long> eventTimes = new ArrayList<>();
    EventAdapter adapter;

    SharedPreferences prefs;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_calendar);

        prefs = getSharedPreferences("EventPrefs", MODE_PRIVATE);

        calendarView   = findViewById(R.id.calendarView);
        etEventTitle   = findViewById(R.id.etEventTitle);
        btnPickTime    = findViewById(R.id.btnPickTime);
        btnAddEvent    = findViewById(R.id.btnAddEvent);
        tvSelectedTime = findViewById(R.id.tvSelectedTime);
        lvEvents       = findViewById(R.id.lvEvents);

        adapter = new EventAdapter(this, eventTitles, eventTimes);
        lvEvents.setAdapter(adapter);

        loadEvents();

        calendarView.setOnDateChangeListener((view, year, month, day) -> {
            Calendar cal = Calendar.getInstance();
            cal.set(year, month, day, 0, 0, 0);
            selectedDateMillis = cal.getTimeInMillis();
        });

        btnPickTime.setOnClickListener(v -> {
            Calendar now = Calendar.getInstance();
            new TimePickerDialog(this, (tp, hour, minute) -> {
                selectedHour   = hour;
                selectedMinute = minute;
                tvSelectedTime.setText(
                    String.format("Selected time: %02d:%02d", hour, minute));
            }, now.get(Calendar.HOUR_OF_DAY), now.get(Calendar.MINUTE), true).show();
        });

        btnAddEvent.setOnClickListener(v -> {
            String title = etEventTitle.getText().toString().trim();
            if (title.isEmpty()) {
                Toast.makeText(this, "Enter event title", Toast.LENGTH_SHORT).show();
                return;
            }
            if (selectedHour < 0) {
                Toast.makeText(this, "Pick a time first", Toast.LENGTH_SHORT).show();
                return;
            }

            Calendar eventCal = Calendar.getInstance();
            eventCal.setTimeInMillis(selectedDateMillis);
            eventCal.set(Calendar.HOUR_OF_DAY, selectedHour);
            eventCal.set(Calendar.MINUTE, selectedMinute);
            eventCal.set(Calendar.SECOND, 0);
            eventCal.set(Calendar.MILLISECOND, 0);

            long triggerTime = eventCal.getTimeInMillis();
            if (triggerTime <= System.currentTimeMillis()) {
                Toast.makeText(this, "Please select a future time", Toast.LENGTH_SHORT).show();
                return;
            }

            saveEvent(title, triggerTime);
            scheduleCalendarNotification(title, triggerTime);
            etEventTitle.setText("");
            Toast.makeText(this, "Event scheduled: " + title, Toast.LENGTH_SHORT).show();
        });
    }

    // Save event to SharedPreferences using title as key
    private void saveEvent(String title, long triggerMillis) {
        prefs.edit()
                .putLong("event_" + triggerMillis, triggerMillis)
                .putString("title_" + triggerMillis, title)
                .apply();
        loadEvents();
    }

    // Load all saved events, sorted by time (earliest first)
    private void loadEvents() {
        eventTitles.clear();
        eventTimes.clear();

        Map<String, ?> all = prefs.getAll();
        List<Long> times = new ArrayList<>();

        for (String key : all.keySet()) {
            if (key.startsWith("event_")) {
                times.add((Long) all.get(key));
            }
        }

        // Sort by time ascending (nearest deadline first)
        Collections.sort(times);

        long now = System.currentTimeMillis();
        for (long t : times) {
            // Only show future events
            if (t > now) {
                String title = prefs.getString("title_" + t, "Event");
                eventTimes.add(t);
                eventTitles.add(title);
            }
        }

        adapter.notifyDataSetChanged();
    }

    private void scheduleCalendarNotification(String title, long triggerMillis) {
        AlarmManager am = (AlarmManager) getSystemService(Context.ALARM_SERVICE);

        Intent intent = new Intent(this, CalendarReceiver.class);
        intent.putExtra("event_title", title);

        int requestCode = (int) (triggerMillis % Integer.MAX_VALUE);

        PendingIntent pi = PendingIntent.getBroadcast(this, requestCode, intent,
                PendingIntent.FLAG_UPDATE_CURRENT | PendingIntent.FLAG_IMMUTABLE);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.S) {
            if (am.canScheduleExactAlarms()) {
                am.setExactAndAllowWhileIdle(AlarmManager.RTC_WAKEUP, triggerMillis, pi);
            } else {
                am.set(AlarmManager.RTC_WAKEUP, triggerMillis, pi);
            }
        } else {
            am.setExactAndAllowWhileIdle(AlarmManager.RTC_WAKEUP, triggerMillis, pi);
        }
    }

    @Override
    protected void onResume() {
        super.onResume();
        loadEvents();
    }
}



