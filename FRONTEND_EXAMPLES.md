# Frontend Implementation Examples

## JavaScript/React Example

### 1. Generate QR Code Token
```javascript
// Generate temporary token for patient
async function generatePatientToken(patientId, expirationMinutes = 60) {
  try {
    const response = await fetch(`/api/patient/tokens/generate/${patientId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${userToken}`,
      },
      body: JSON.stringify({
        expiration_minutes: expirationMinutes
      })
    });

    const data = await response.json();
    
    if (data.status === 'success') {
      return {
        token: data.data.token,
        qrUrl: data.data.qr_url,
        expiresAt: data.data.expires_at,
        patient: data.data.patient
      };
    }
    throw new Error(data.message);
  } catch (error) {
    console.error('Error generating token:', error);
    throw error;
  }
}

// Usage
const tokenData = await generatePatientToken(123, 30); // 30 minutes
console.log('QR URL:', tokenData.qrUrl);
```

### 2. QR Code Generation with qrcode.js
```javascript
import QRCode from 'qrcode';

async function generateQRCode(tokenData) {
  try {
    // Generate QR code as data URL
    const qrDataURL = await QRCode.toDataURL(tokenData.qrUrl, {
      errorCorrectionLevel: 'M',
      type: 'image/png',
      quality: 0.92,
      margin: 1,
      color: {
        dark: '#000000',
        light: '#FFFFFF'
      },
      width: 256
    });

    return qrDataURL;
  } catch (error) {
    console.error('Error generating QR code:', error);
    throw error;
  }
}

// React Component Example
function QRCodeGenerator({ patientId }) {
  const [qrCode, setQrCode] = useState(null);
  const [tokenData, setTokenData] = useState(null);
  const [loading, setLoading] = useState(false);

  const handleGenerateQR = async () => {
    setLoading(true);
    try {
      const data = await generatePatientToken(patientId, 60);
      const qrDataURL = await generateQRCode(data);
      
      setTokenData(data);
      setQrCode(qrDataURL);
    } catch (error) {
      alert('Error generating QR code: ' + error.message);
    } finally {
      setLoading(false);
    }
  };

  const handleRevokeToken = async () => {
    if (!tokenData) return;
    
    try {
      await fetch(`/api/patient/tokens/revoke/${tokenData.token}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${userToken}`,
        }
      });
      
      setQrCode(null);
      setTokenData(null);
      alert('Token revoked successfully');
    } catch (error) {
      alert('Error revoking token: ' + error.message);
    }
  };

  return (
    <div className="qr-generator">
      <h3>Patient QR Code Generator</h3>
      
      {!qrCode ? (
        <button onClick={handleGenerateQR} disabled={loading}>
          {loading ? 'Generating...' : 'Generate QR Code'}
        </button>
      ) : (
        <div className="qr-display">
          <img src={qrCode} alt="Patient QR Code" />
          <div className="token-info">
            <p>Patient: {tokenData.patient.full_name}</p>
            <p>Expires: {new Date(tokenData.expiresAt).toLocaleString()}</p>
            <button onClick={handleRevokeToken} className="revoke-btn">
              Revoke Token
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
```

### 3. Access Patient Data (Scanner Device)
```javascript
// Function to access patient data using scanned token
async function accessPatientData(token) {
  try {
    const response = await fetch(`/api/patient/token/${token}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      }
    });

    const data = await response.json();
    
    if (data.status === 'success') {
      return {
        patientData: data.data.patient_data,
        tokenInfo: data.data.token_info
      };
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Error accessing patient data:', error);
    throw error;
  }
}

// QR Scanner Component
function QRScanner() {
  const [scanning, setScanning] = useState(false);
  const [patientData, setPatientData] = useState(null);
  const [error, setError] = useState(null);

  const handleScan = async (scanResult) => {
    if (!scanResult) return;
    
    try {
      setScanning(false);
      setError(null);
      
      // Extract token from URL
      const url = new URL(scanResult);
      const token = url.pathname.split('/').pop();
      
      const data = await accessPatientData(token);
      setPatientData(data);
      
    } catch (error) {
      setError(error.message);
    }
  };

  if (patientData) {
    return <PatientDataDisplay data={patientData} />;
  }

  return (
    <div className="qr-scanner">
      {scanning ? (
        <QrReader
          onResult={handleScan}
          onError={(error) => setError(error?.message)}
          style={{ width: '100%' }}
        />
      ) : (
        <button onClick={() => setScanning(true)}>
          Scan QR Code
        </button>
      )}
      
      {error && (
        <div className="error">
          Error: {error}
        </div>
      )}
    </div>
  );
}
```

## Flutter/Dart Example

### 1. Generate Token & QR Code
```dart
import 'package:qr_flutter/qr_flutter.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class TokenService {
  static const String baseUrl = 'https://your-api.com';
  
  static Future<Map<String, dynamic>> generateToken(
    int patientId, 
    int expirationMinutes,
    String userToken
  ) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/api/patient/tokens/generate/$patientId'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $userToken',
        },
        body: jsonEncode({
          'expiration_minutes': expirationMinutes,
        }),
      );
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['status'] == 'success') {
          return data['data'];
        }
        throw Exception(data['message']);
      }
      throw Exception('Failed to generate token');
    } catch (e) {
      throw Exception('Error generating token: $e');
    }
  }
  
  static Future<Map<String, dynamic>> accessPatientData(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/api/patient/token/$token'),
        headers: {
          'Content-Type': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['status'] == 'success') {
          return data['data'];
        }
        throw Exception(data['message']);
      }
      throw Exception('Failed to access patient data');
    } catch (e) {
      throw Exception('Error accessing patient data: $e');
    }
  }
}

// QR Generator Widget
class QRGeneratorWidget extends StatefulWidget {
  final int patientId;
  final String userToken;
  
  const QRGeneratorWidget({
    Key? key, 
    required this.patientId,
    required this.userToken,
  }) : super(key: key);

  @override
  _QRGeneratorWidgetState createState() => _QRGeneratorWidgetState();
}

class _QRGeneratorWidgetState extends State<QRGeneratorWidget> {
  Map<String, dynamic>? tokenData;
  bool isLoading = false;
  String? error;

  Future<void> generateQR() async {
    setState(() {
      isLoading = true;
      error = null;
    });

    try {
      final data = await TokenService.generateToken(
        widget.patientId, 
        60, // 1 hour
        widget.userToken
      );
      
      setState(() {
        tokenData = data;
      });
    } catch (e) {
      setState(() {
        error = e.toString();
      });
    } finally {
      setState(() {
        isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        if (tokenData == null) ...[
          ElevatedButton(
            onPressed: isLoading ? null : generateQR,
            child: isLoading 
              ? CircularProgressIndicator() 
              : Text('Generate QR Code'),
          ),
        ] else ...[
          QrImageView(
            data: tokenData!['qr_url'],
            version: QrVersions.auto,
            size: 200.0,
          ),
          Text('Patient: ${tokenData!['patient']['full_name']}'),
          Text('Expires: ${tokenData!['expires_at']}'),
          ElevatedButton(
            onPressed: () {
              setState(() {
                tokenData = null;
              });
            },
            child: Text('Generate New'),
          ),
        ],
        if (error != null)
          Text(
            'Error: $error',
            style: TextStyle(color: Colors.red),
          ),
      ],
    );
  }
}
```

### 2. QR Scanner Widget
```dart
import 'package:mobile_scanner/mobile_scanner.dart';

class QRScannerWidget extends StatefulWidget {
  @override
  _QRScannerWidgetState createState() => _QRScannerWidgetState();
}

class _QRScannerWidgetState extends State<QRScannerWidget> {
  MobileScannerController cameraController = MobileScannerController();
  bool isProcessing = false;

  void _onDetect(BarcodeCapture capture) async {
    if (isProcessing) return;
    
    final List<Barcode> barcodes = capture.barcodes;
    if (barcodes.isEmpty) return;
    
    setState(() {
      isProcessing = true;
    });

    try {
      final String? code = barcodes.first.rawValue;
      if (code != null) {
        // Extract token from URL
        final uri = Uri.parse(code);
        final token = uri.pathSegments.last;
        
        // Access patient data
        final data = await TokenService.accessPatientData(token);
        
        // Navigate to patient data screen
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => PatientDataScreen(data: data),
          ),
        );
      }
    } catch (e) {
      showDialog(
        context: context,
        builder: (context) => AlertDialog(
          title: Text('Error'),
          content: Text(e.toString()),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text('OK'),
            ),
          ],
        ),
      );
    } finally {
      setState(() {
        isProcessing = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Scan QR Code')),
      body: Stack(
        children: [
          MobileScanner(
            controller: cameraController,
            onDetect: _onDetect,
          ),
          if (isProcessing)
            Center(
              child: CircularProgressIndicator(),
            ),
        ],
      ),
    );
  }
}
```

## Security Best Practices

### 1. Token Handling
```javascript
// Never log full tokens
console.log('Token generated:', token.substring(0, 10) + '...');

// Clear sensitive data from memory
function clearTokenData(tokenObj) {
  if (tokenObj && tokenObj.token) {
    tokenObj.token = null;
  }
}

// Validate token format before sending
function validateTokenFormat(token) {
  return typeof token === 'string' && token.length === 64;
}
```

### 2. Error Handling
```javascript
// Generic error handling
function handleTokenError(error, context = '') {
  console.error(`Token error ${context}:`, error);
  
  // Don't expose sensitive information to user
  const userMessage = error.message.includes('expired') 
    ? 'QR code has expired. Please generate a new one.'
    : 'Unable to access patient data. Please try again.';
    
  return userMessage;
}
```

### 3. UI/UX Considerations
```javascript
// Show countdown timer for token expiry
function TokenExpiryTimer({ expiresAt, onExpired }) {
  const [timeLeft, setTimeLeft] = useState(null);

  useEffect(() => {
    const timer = setInterval(() => {
      const now = new Date();
      const expiry = new Date(expiresAt);
      const diff = expiry - now;
      
      if (diff <= 0) {
        onExpired();
        clearInterval(timer);
      } else {
        setTimeLeft(Math.floor(diff / 1000));
      }
    }, 1000);

    return () => clearInterval(timer);
  }, [expiresAt, onExpired]);

  if (!timeLeft) return null;

  const minutes = Math.floor(timeLeft / 60);
  const seconds = timeLeft % 60;

  return (
    <div className="timer">
      Expires in: {minutes}:{seconds.toString().padStart(2, '0')}
    </div>
  );
}
```

---

## Integration Checklist

- [ ] Install QR code libraries
- [ ] Implement token generation UI
- [ ] Add QR code display component
- [ ] Create QR scanner functionality
- [ ] Add patient data display
- [ ] Implement error handling
- [ ] Add loading states
- [ ] Test token expiry
- [ ] Test revocation
- [ ] Add user feedback
- [ ] Security review
- [ ] Production testing
