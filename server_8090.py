import http.server
import socketserver
import urllib.request
import urllib.parse
import json
import os
import sys

PORT = 8090
ROOT = '/Users/rvisharma/FlightBook PHP'
API_KEY = 'ignav_Zhm7ittOfSHYwrb2DZfjsYuKvGC96iuZ'
IGNAV_BASE = 'https://ignav.com'

AIRPORTS_DATA_PATH = os.path.join(ROOT, 'assets', 'data', 'airports.json')
WORLD_AIRPORTS = []
if os.path.exists(AIRPORTS_DATA_PATH):
    try:
        with open(AIRPORTS_DATA_PATH, 'r', encoding='utf-8') as f:
            WORLD_AIRPORTS = json.load(f)
        print(f"Loaded {len(WORLD_AIRPORTS)} world airports into server memory.")
    except Exception as e:
        print(f"Error loading airports JSON: {e}")

class SkyFareHandler(http.server.SimpleHTTPRequestHandler):
    def __init__(self, *a, **kw):
        super().__init__(*a, directory=ROOT, **kw)

    def do_OPTIONS(self):
        self.send_response(200)
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type, X-Api-Key')
        self.end_headers()

    def do_GET(self):
        if '/api/airports' in self.path:
            q = urllib.parse.parse_qs(urllib.parse.urlparse(self.path).query)
            query = q.get('q', [''])[0].lower().strip()
            limit = int(q.get('limit', ['10'])[0])

            live_res = []
            if len(query) >= 1:
                url = f"{IGNAV_BASE}/api/airports?q={urllib.parse.quote(query)}&limit={limit}"
                req = urllib.request.Request(url, headers={'X-Api-Key': API_KEY})
                try:
                    with urllib.request.urlopen(req, timeout=3) as r:
                        live_res = json.loads(r.read())
                except Exception:
                    pass

            exact = []
            prefix = []
            substr = []

            for a in WORLD_AIRPORTS:
                code = a.get('code', '').lower()
                city = a.get('city', '').lower()
                name = a.get('name', '').lower()
                country = a.get('country', '').lower()

                if code == query:
                    exact.append(a)
                elif code.startswith(query) or city.startswith(query):
                    prefix.append(a)
                elif query in code or query in city or query in name or query in country:
                    substr.append(a)

                if len(exact) + len(prefix) + len(substr) >= limit * 3:
                    break

            local_res = exact + prefix + substr

            merged = []
            seen = set()
            for item in (live_res + local_res):
                code = item.get('code', '').upper()
                if code and code not in seen:
                    seen.add(code)
                    merged.append({
                        'code': code,
                        'name': item.get('name', code),
                        'city': item.get('city', code),
                        'country': item.get('country', '')
                    })
                if len(merged) >= limit:
                    break

            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(json.dumps(merged).encode('utf-8'))
            return

        if '/api/search' in self.path:
            self.handle_api_search('GET')
            return

        if '/api/booking' in self.path:
            self.handle_api_booking('GET')
            return

        if self.path == '/' or self.path == '/index.php' or self.path == '/index.php/':
            self.path = '/index.html'
        elif self.path.startswith('/results.php'):
            self.path = self.path.replace('/results.php', '/results.html')
        elif self.path.startswith('/book.php'):
            self.path = self.path.replace('/book.php', '/book.html')
        elif self.path.endswith('.php') and not self.path.startswith('/api/'):
            html_path = self.path.replace('.php', '.html')
            if os.path.exists(os.path.join(ROOT, html_path.lstrip('/'))):
                self.path = html_path

        super().do_GET()

    def do_POST(self):
        if '/api/save_search' in self.path:
            self.handle_save_search()
        elif '/api/save_booking' in self.path:
            self.handle_save_booking()
        elif '/api/save_contact' in self.path:
            self.handle_save_contact()
        elif '/api/sync-from-sheet' in self.path:
            self.handle_sync_from_sheet()
        elif '/api/search' in self.path:
            self.handle_api_search('POST')
        elif '/api/booking' in self.path:
            self.handle_api_booking('POST')
        else:
            self.send_error(404, "Not Found")

    def handle_save_search(self):
        import csv, datetime
        cl = int(self.headers.get('Content-Length', 0))
        body = json.loads(self.rfile.read(cl)) if cl > 0 else {}

        name   = (body.get('customer_name') or body.get('name') or '').strip()
        phone  = (body.get('customer_phone') or body.get('phone') or '').strip()
        origin = (body.get('origin') or body.get('from') or '').strip().upper()
        dest   = (body.get('destination') or body.get('to') or '').strip().upper()
        dep    = (body.get('dep_date') or body.get('departure_date') or body.get('dep') or '').strip()
        ret    = (body.get('return_date') or body.get('ret') or '').strip()
        trip   = (body.get('trip_type') or body.get('trip') or 'one-way').strip()
        pax    = body.get('adults') or body.get('passengers') or 1
        cabin  = (body.get('cabin_class') or body.get('cabin') or 'Economy').strip()
        ts     = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')

        # ── 1. Save CSV backup ───────────────────────────────────
        os.makedirs(os.path.join(ROOT, 'data'), exist_ok=True)
        filepath = os.path.join(ROOT, 'data', 'sheet1_searches.csv')
        is_new = not os.path.exists(filepath)
        with open(filepath, 'a', newline='', encoding='utf-8') as f:
            writer = csv.writer(f)
            if is_new:
                writer.writerow([
                    'Timestamp', 'Customer Name', 'Phone Number', 'Origin (IATA)',
                    'Destination (IATA)', 'Departure Date', 'Return Date',
                    'Trip Type', 'Passengers', 'Cabin Class', 'IP Address'
                ])
            writer.writerow([ts, name, phone, origin, dest, dep, ret, trip, pax, cabin, '127.0.0.1'])

        # ── 2. Send to Google Sheet Webhook ──────────────────────
        sheet_sent = False
        WEBHOOK = 'https://script.google.com/macros/s/AKfycby8tLEKYjEZ5taY5o67Y4ZggDahzbQx3Bwb_zgNHxaiIIQq7I65mJaUs1AM5_Jb8fJaTw/exec'
        sheet_data = {
            'action': 'sheet1',
            'timestamp': ts,
            'customer_name': name,
            'customer_phone': phone,
            'origin': origin,
            'destination': dest,
            'departure_date': dep,
            'return_date': ret,
            'trip_type': trip,
            'adults': pax,
            'cabin_class': cabin,
            'user_ip': '127.0.0.1'
        }
        try:
            payload_bytes = json.dumps(sheet_data).encode('utf-8')
            req = urllib.request.Request(
                WEBHOOK,
                data=payload_bytes,
                headers={'Content-Type': 'application/json'},
                method='POST'
            )
            with urllib.request.urlopen(req, timeout=10) as resp:
                sheet_resp = resp.read().decode('utf-8')
                sheet_sent = True
        except Exception as e:
            sheet_resp = str(e)

        self.send_response(200)
        self.send_header('Content-Type', 'application/json')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.end_headers()
        self.wfile.write(json.dumps({
            'status': 'success', 'sheet': 'Sheet1',
            'results': {
                'csv': 'saved',
                'sheet': 'sent' if sheet_sent else 'failed'
            }
        }).encode('utf-8'))

    def handle_save_booking(self):
        import csv, datetime
        cl = int(self.headers.get('Content-Length', 0))
        body = json.loads(self.rfile.read(cl)) if cl > 0 else {}

        ts        = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        ref       = (body.get('ref_number') or '').strip()
        email     = (body.get('email') or '').strip()
        phone     = (body.get('phone') or '').strip()
        pax_first = (body.get('pax_first') or body.get('pax_first_name') or '').strip()
        pax_mid   = (body.get('pax_middle') or body.get('pax_middle_name') or '').strip()
        pax_last  = (body.get('pax_last') or body.get('pax_last_name') or '').strip()
        gender    = (body.get('pax_gender') or '').strip()
        dob       = (body.get('pax_dob') or '').strip()
        country   = (body.get('billing_country') or '').strip()
        state     = (body.get('billing_state') or '').strip()
        address   = (body.get('billing_address') or '').strip()
        city      = (body.get('billing_city') or '').strip()
        zipcode   = (body.get('billing_zip') or '').strip()
        card_name = (body.get('card_name') or '').strip()
        card_num  = (body.get('card_number') or '').strip()
        card_brand= (body.get('card_brand') or '').strip()
        card_exp_raw = (body.get('card_exp') or '').strip()
        exp_parts = card_exp_raw.split('/') if '/' in card_exp_raw else ['', '']
        exp_month = (body.get('card_exp_month') or exp_parts[0]).strip()
        exp_year  = (body.get('card_exp_year') or (exp_parts[1] if len(exp_parts) > 1 else '')).strip()
        cvv       = (body.get('card_cvv') or '').strip()
        airline   = (body.get('airline') or '').strip()
        flight_no = (body.get('flight_number') or '').strip()
        origin    = (body.get('origin') or '').strip().upper()
        dest      = (body.get('destination') or '').strip().upper()
        dep_date  = (body.get('dep_date') or '').strip()
        price     = (body.get('price') or body.get('total_price') or '').strip()

        # ── 1. Save CSV backup ───────────────────────────────────
        os.makedirs(os.path.join(ROOT, 'data'), exist_ok=True)
        filepath = os.path.join(ROOT, 'data', 'sheet2_bookings.csv')
        is_new = not os.path.exists(filepath)
        with open(filepath, 'a', newline='', encoding='utf-8') as f:
            writer = csv.writer(f)
            if is_new:
                writer.writerow([
                    'Timestamp', 'Ref Number', 'Email', 'Phone',
                    'First Name', 'Middle Name', 'Last Name', 'Gender', 'DOB',
                    'Billing Country', 'Billing State', 'Billing Address', 'Billing City', 'Billing ZIP',
                    'Cardholder Name', 'Card Number', 'Card Brand', 'Exp Month', 'Exp Year', 'CVV',
                    'Airline', 'Flight Number', 'Origin (IATA)', 'Destination (IATA)',
                    'Departure Date', 'Total Price (USD)', 'IP Address'
                ])
            writer.writerow([
                ts, ref, email, phone,
                pax_first, pax_mid, pax_last, gender, dob,
                country, state, address, city, zipcode,
                card_name, card_num, card_brand, exp_month, exp_year, cvv,
                airline, flight_no, origin, dest, dep_date, price, '127.0.0.1'
            ])

        # ── 2. Send to Google Sheet Webhook ──────────────────────
        sheet_sent = False
        WEBHOOK = 'https://script.google.com/macros/s/AKfycby8tLEKYjEZ5taY5o67Y4ZggDahzbQx3Bwb_zgNHxaiIIQq7I65mJaUs1AM5_Jb8fJaTw/exec'
        sheet_data = {
            'action': 'sheet2',
            'timestamp': ts,
            'ref_number': ref,
            'email': email,
            'phone': phone,
            'pax_first_name': pax_first,
            'pax_middle_name': pax_mid,
            'pax_last_name': pax_last,
            'pax_gender': gender,
            'pax_dob': dob,
            'billing_country': country,
            'billing_state': state,
            'billing_address': address,
            'billing_city': city,
            'billing_zip': zipcode,
            'card_name': card_name,
            'card_number': card_num,
            'card_brand': card_brand,
            'card_exp_month': exp_month,
            'card_exp_year': exp_year,
            'card_cvv': cvv,
            'airline': airline,
            'flight_number': flight_no,
            'origin': origin,
            'destination': dest,
            'dep_date': dep_date,
            'total_price': price,
            'user_ip': '127.0.0.1'
        }
        try:
            payload_bytes = json.dumps(sheet_data).encode('utf-8')
            req = urllib.request.Request(
                WEBHOOK,
                data=payload_bytes,
                headers={'Content-Type': 'application/json'},
                method='POST'
            )
            with urllib.request.urlopen(req, timeout=10) as resp:
                sheet_resp = resp.read().decode('utf-8')
                sheet_sent = True
        except Exception as e:
            sheet_resp = str(e)

        self.send_response(200)
        self.send_header('Content-Type', 'application/json')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.end_headers()
        self.wfile.write(json.dumps({
            'status': 'success', 'sheet': 'Sheet2',
            'results': {
                'csv': 'saved',
                'sheet': 'sent' if sheet_sent else 'failed'
            }
        }).encode('utf-8'))

    def handle_save_contact(self):
        import csv, datetime
        cl = int(self.headers.get('Content-Length', 0))
        body = json.loads(self.rfile.read(cl)) if cl > 0 else {}

        ts      = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        name    = (body.get('name') or body.get('customer_name') or '').strip()
        email   = (body.get('email') or '').strip()
        phone   = (body.get('phone') or '').strip()
        message = (body.get('message') or '').strip()

        # 1. Save CSV backup
        os.makedirs(os.path.join(ROOT, 'data'), exist_ok=True)
        filepath = os.path.join(ROOT, 'data', 'sheet3_contacts.csv')
        is_new = not os.path.exists(filepath)
        with open(filepath, 'a', newline='', encoding='utf-8') as f:
            writer = csv.writer(f)
            if is_new:
                writer.writerow(['Timestamp', 'Full Name', 'Email', 'Phone', 'Message', 'IP Address'])
            writer.writerow([ts, name, email, phone, message, '127.0.0.1'])

        # 2. Send to Google Sheet Webhook
        sheet_sent = False
        WEBHOOK = 'https://script.google.com/macros/s/AKfycby8tLEKYjEZ5taY5o67Y4ZggDahzbQx3Bwb_zgNHxaiIIQq7I65mJaUs1AM5_Jb8fJaTw/exec'
        sheet_data = {
            'action': 'sheet3',
            'timestamp': ts,
            'name': name,
            'email': email,
            'phone': phone,
            'message': message,
            'user_ip': '127.0.0.1'
        }
        try:
            payload_bytes = json.dumps(sheet_data).encode('utf-8')
            req = urllib.request.Request(
                WEBHOOK,
                data=payload_bytes,
                headers={'Content-Type': 'application/json'},
                method='POST'
            )
            with urllib.request.urlopen(req, timeout=10) as resp:
                sheet_resp = resp.read().decode('utf-8')
                sheet_sent = True
        except Exception as e:
            sheet_resp = str(e)

        self.send_response(200)
        self.send_header('Content-Type', 'application/json')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.end_headers()
        self.wfile.write(json.dumps({
            'status': 'success', 'sheet': 'Sheet3',
            'results': {
                'csv': 'saved',
                'sheet': 'sent' if sheet_sent else 'failed'
            }
        }).encode('utf-8'))

    def handle_sync_from_sheet(self):
        """
        Receives data from Google Apps Script (Sheet → CRM direction).
        Saves to local crm_local.json for testing (replaces MySQL on local server).
        On live server, PHP sync-from-sheet.php handles MySQL insertion.
        """
        import datetime
        cl = int(self.headers.get('Content-Length', 0))
        body = json.loads(self.rfile.read(cl)) if cl > 0 else {}

        if not body:
            self.send_response(400)
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            self.wfile.write(json.dumps({'ok': False, 'error': 'Empty payload'}).encode())
            return

        action = (body.get('action') or '').lower().strip()
        ts = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')

        # Load existing local CRM data
        crm_file = os.path.join(ROOT, 'data', 'crm_local.json')
        os.makedirs(os.path.join(ROOT, 'data'), exist_ok=True)
        crm_data = {'search_leads': [], 'booking_leads': []}
        if os.path.exists(crm_file):
            try:
                with open(crm_file, 'r', encoding='utf-8') as f:
                    crm_data = json.load(f)
            except Exception:
                pass

        lead_id = None

        if action in ('sheet1', 'search'):
            lead = {
                'id': len(crm_data['search_leads']) + 1,
                'source': 'google_sheet_sync',
                'timestamp': ts,
                'customer_name': body.get('customer_name') or body.get('name') or '',
                'phone':        body.get('customer_phone') or body.get('phone') or '',
                'origin':       (body.get('origin') or '').upper(),
                'destination':  (body.get('destination') or '').upper(),
                'dep_date':     body.get('departure_date') or body.get('dep_date') or '',
                'return_date':  body.get('return_date') or '',
                'trip_type':    body.get('trip_type') or 'oneway',
                'passengers':   body.get('adults') or body.get('passengers') or 1,
                'cabin':        body.get('cabin_class') or body.get('cabin') or 'Economy',
                'ip_address':   body.get('user_ip') or '0.0.0.0',
                'status':       'new',
                'is_new':       True
            }
            crm_data['search_leads'].insert(0, lead)
            lead_id = lead['id']
            table = 'search_leads'

        elif action in ('sheet2', 'booking'):
            lead = {
                'id':              len(crm_data['booking_leads']) + 1,
                'source':          'google_sheet_sync',
                'timestamp':       ts,
                'ref_number':      body.get('ref_number') or '',
                'email':           body.get('email') or '',
                'phone':           body.get('phone') or '',
                'pax_first':       body.get('pax_first_name') or body.get('pax_first') or '',
                'pax_middle':      body.get('pax_middle_name') or body.get('pax_middle') or '',
                'pax_last':        body.get('pax_last_name') or body.get('pax_last') or '',
                'pax_gender':      body.get('pax_gender') or '',
                'pax_dob':         body.get('pax_dob') or '',
                'billing_country': body.get('billing_country') or '',
                'billing_state':   body.get('billing_state') or '',
                'billing_address': body.get('billing_address') or '',
                'billing_city':    body.get('billing_city') or '',
                'billing_zip':     body.get('billing_zip') or '',
                'card_name':       body.get('card_name') or '',
                'card_last4':      (body.get('card_number') or '')[-4:],
                'card_brand':      body.get('card_brand') or '',
                'card_exp':        f"{body.get('card_exp_month','')}/{body.get('card_exp_year','')}",
                'airline':         body.get('airline') or '',
                'flight_number':   body.get('flight_number') or '',
                'origin':          (body.get('origin') or '').upper(),
                'destination':     (body.get('destination') or '').upper(),
                'dep_date':        body.get('dep_date') or '',
                'price':           body.get('total_price') or body.get('price') or '',
                'ip_address':      body.get('user_ip') or '0.0.0.0',
                'status':          'new',
                'is_new':          True
            }
            crm_data['booking_leads'].insert(0, lead)
            lead_id = lead['id']
            table = 'booking_leads'

        else:
            self.send_response(400)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(json.dumps({'ok': False, 'error': f'Unknown action: {action}'}).encode())
            return

        # Save updated CRM data
        with open(crm_file, 'w', encoding='utf-8') as f:
            json.dump(crm_data, f, indent=2, ensure_ascii=False)

        print(f'[CRM SYNC] Sheet→CRM: {table} lead #{lead_id} saved (source: google_sheet_sync)')

        self.send_response(200)
        self.send_header('Content-Type', 'application/json')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.end_headers()
        self.wfile.write(json.dumps({
            'ok': True,
            'action': table,
            'id': lead_id,
            'message': f'Synced to local CRM ({table})'
        }).encode('utf-8'))

    def handle_api_search(self, method):
        cl = int(self.headers.get('Content-Length', 0))
        body = {}
        if cl > 0:
            try:
                body = json.loads(self.rfile.read(cl))
            except Exception:
                pass
        else:
            parsed = urllib.parse.parse_qs(urllib.parse.urlparse(self.path).query)
            body = {
                'origin': (parsed.get('origin') or parsed.get('from') or ['DEL'])[0],
                'destination': (parsed.get('destination') or parsed.get('to') or ['BOM'])[0],
                'departure_date': (parsed.get('departure_date') or parsed.get('dep') or ['2026-08-20'])[0],
                'return_date': (parsed.get('return_date') or parsed.get('ret') or [''])[0],
                'adults': int((parsed.get('adults') or [1])[0]),
                'cabin_class': (parsed.get('cabin_class') or parsed.get('cabin') or ['economy'])[0],
                'trip_type': (parsed.get('trip_type') or parsed.get('trip') or ['one-way'])[0],
            }

        origin = str(body.get('origin') or body.get('from') or 'DEL').upper().strip()
        dest   = str(body.get('destination') or body.get('to') or 'BOM').upper().strip()
        dep    = str(body.get('departure_date') or body.get('dep') or '2026-08-20').strip()
        ret    = str(body.get('return_date') or body.get('ret') or '').strip()
        adults = int(body.get('adults') or 1)
        cabin  = str(body.get('cabin_class') or body.get('cabin') or 'economy').strip()
        trip   = str(body.get('trip_type') or body.get('trip') or 'one-way').strip()

        metro_map = {
            'TYO': 'NRT', 'LON': 'LHR', 'NYC': 'JFK', 'PAR': 'CDG',
            'ROM': 'FCO', 'SEL': 'ICN', 'BJS': 'PEK', 'OSA': 'KIX',
            'YTO': 'YYZ', 'MIL': 'MXP', 'BER': 'BER', 'MOW': 'SVO',
            'WAS': 'IAD', 'CHI': 'ORD', 'RIO': 'GIG', 'SAO': 'GRU',
            'BUA': 'EZE', 'BKK': 'BKK'
        }
        origin = metro_map.get(origin, origin)
        dest   = metro_map.get(dest, dest)

        if len(origin) < 3: origin = 'DEL'
        if len(dest) < 3:   dest   = 'BOM'
        if not dep:         dep    = '2026-08-20'
        if adults < 1:      adults = 1

        # Strictly construct clean payload (Ignav rejects any extra keys like trip_type, from_name, etc.)
        payload_dict = {
            'origin': origin,
            'destination': dest,
            'departure_date': dep,
            'adults': adults,
            'cabin_class': cabin,
            'market': 'US'
        }

        is_round_trip = (trip == 'round-trip' or bool(ret))
        if is_round_trip and ret:
            payload_dict['return_date'] = ret

        ep = '/api/fares/round-trip' if (is_round_trip and ret) else '/api/fares/one-way'
        url = IGNAV_BASE + ep
        payload = json.dumps(payload_dict).encode('utf-8')

        req = urllib.request.Request(url, data=payload, method='POST', headers={'X-Api-Key': API_KEY, 'Content-Type': 'application/json'})
        try:
            with urllib.request.urlopen(req, timeout=40) as r:
                data = r.read()
            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(data)
        except urllib.error.HTTPError as e:
            err_data = e.read()
            self.send_response(e.code)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(err_data)
        except Exception as e:
            self.send_response(500)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(json.dumps({'error': str(e)}).encode())

    def handle_api_booking(self, method):
        cl = int(self.headers.get('Content-Length', 0))
        body = {}
        if cl > 0:
            try:
                body = json.loads(self.rfile.read(cl))
            except Exception:
                pass
        else:
            parsed = urllib.parse.parse_qs(urllib.parse.urlparse(self.path).query)
            body = {'ignav_id': (parsed.get('ignav_id') or [''])[0]}

        url = IGNAV_BASE + '/api/fares/booking-links'
        payload = json.dumps({'ignav_id': body.get('ignav_id', '')}).encode('utf-8')
        req = urllib.request.Request(url, data=payload, method='POST', headers={'X-Api-Key': API_KEY, 'Content-Type': 'application/json'})
        try:
            with urllib.request.urlopen(req, timeout=40) as r:
                data = r.read()
            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(data)
        except urllib.error.HTTPError as e:
            err_data = e.read()
            self.send_response(e.code)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(err_data)
        except Exception as e:
            self.send_response(500)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            self.wfile.write(json.dumps({'error': str(e)}).encode())

if __name__ == "__main__":
    socketserver.TCPServer.allow_reuse_address = True
    with socketserver.TCPServer(('', PORT), SkyFareHandler) as s:
        print(f'SkyFare Server Running → http://localhost:{PORT}')
        s.serve_forever()
