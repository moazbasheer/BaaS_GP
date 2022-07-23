import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter/scheduler.dart';
import 'package:flutter/services.dart';
import 'package:flutter_credit_card/credit_card_brand.dart';
import 'package:flutter_credit_card/flutter_credit_card.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:learningdart/Login.dart';
import 'package:learningdart/walletOption.dart';
import 'package:http/http.dart' as http;

Future<chargingInfo> ChargingWallet(Map data) async {
  final response;
  if (rolename == "client") {
    response = await http.post(
      Uri.parse('http://baas-gp.herokuapp.com/api/client/wallet/charge'),
      headers: {
        'Content-Type': 'application/json; charset=UTF-8',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
      body: jsonEncode(<String, String>{
        'card_number': data['card_number'],
        'exp_month': data['exp_month'],
        'exp_year': data['exp_year'],
        'CVC': data['CVC'],
        'amount': data['amount']
      }),
    );
  } else {
    response = await http.post(
      Uri.parse('http://baas-gp.herokuapp.com/api/passenger/wallet/charge'),
      headers: {
        'Content-Type': 'application/json; charset=UTF-8',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: jsonEncode(<String, String>{
        'card_number': data['card_number'],
        'exp_month': data['exp_month'],
        'exp_year': data['exp_year'],
        'CVC': data['CVC'],
        'amount': data['amount']
      }),
    );
  }
  if (response.statusCode == 200) {
    return chargingInfo.fromJson(jsonDecode(response.body));
  } else {
    throw Exception('Failed to charging wallet');
  }
}

class chargingInfo {
  final bool status;
  final String message;
  const chargingInfo({required this.status, required this.message});

  factory chargingInfo.fromJson(Map<String, dynamic> json) {
    return chargingInfo(
      status: json['status'],
      message: json['message'][0],
    );
  }
}

class CreditCardPage extends StatefulWidget {
  final String? rolename;
  final String? email;
  final String? username;
  CreditCardPage({Key? key, this.rolename, this.email, this.username})
      : super(key: key);
  @override
  State<StatefulWidget> createState() {
    return CreditCardPageState();
  }
}

class CreditCardPageState extends State<CreditCardPage> {
  String cardNumber = '';
  String expiryDate = '';
  String cardHolderName = '';
  String cvvCode = '';
  bool isCvvFocused = false;
  bool useGlassMorphism = false;
  bool useBackgroundImage = false;
  OutlineInputBorder? border;
  TextEditingController amountController = TextEditingController();
  final GlobalKey<FormState> formKey1 = GlobalKey<FormState>();
  final GlobalKey<FormState> formKey2 = GlobalKey<FormState>();
  Future<chargingInfo>? charging;

  Map _charging() {
    List<String> expdate = expiryDate.split("/");
    String expYear = "20" + expdate[1];
    List<String> listOfcardNumberPartion = cardNumber.split(" ");
    String cardNumberString = "";
    for (int i = 0; i < listOfcardNumberPartion.length; i++) {
      cardNumberString = cardNumberString + listOfcardNumberPartion[i];
    }
    Map data = {
      'card_number': cardNumberString,
      'exp_month': expdate[0],
      'exp_year': expYear,
      'CVC': cvvCode,
      'amount': amountController.text,
    };
    return data;
  }

  @override
  void initState() {
    border = OutlineInputBorder(
      borderSide: BorderSide(
        color: Colors.grey.withOpacity(0.7),
        width: 2.0,
      ),
    );
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
        debugShowCheckedModeBanner: false,
        theme: ThemeData(
          primarySwatch: Colors.blue,
        ),
        home: Scaffold(
            appBar: AppBar(
              leading: IconButton(
                  icon: Icon(FontAwesomeIcons.arrowLeft),
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (context) => wallet(
                                email: widget.email,
                                username: widget.username,
                                rolename: widget.rolename,
                              )),
                    );
                  }),
              title: Text(
                "Charging Wallet",
                style: TextStyle(fontSize: 25),
              ),
            ),
            resizeToAvoidBottomInset: false,
            body: Container(
                child: charging == null
                    ? buildPaymentPage()
                    : checkChargingResponse())));
  }

  Column buildPaymentPage() {
    return Column(children: <Widget>[
      const SizedBox(
        height: 10,
      ),
      CreditCardWidget(
        glassmorphismConfig:
            useGlassMorphism ? Glassmorphism.defaultConfig() : null,
        cardNumber: cardNumber,
        expiryDate: expiryDate,
        cardHolderName: cardHolderName,
        cvvCode: cvvCode,
        showBackView: isCvvFocused,
        obscureCardNumber: true,
        obscureCardCvv: true,
        isHolderNameVisible: true,
        cardBgColor: Colors.black,
        backgroundImage: useBackgroundImage ? 'assets/card_bg.png' : null,
        isSwipeGestureEnabled: true,
        onCreditCardWidgetChange: (CreditCardBrand creditCardBrand) {},
        customCardTypeIcons: <CustomCardTypeIcon>[
          CustomCardTypeIcon(
            cardType: CardType.mastercard,
            cardImage: Image.asset(
              'images/mastercard.png',
              height: 48,
              width: 48,
            ),
          ),
        ],
      ),
      Expanded(
          child: SingleChildScrollView(
              controller: ScrollController(),
              child: Column(
                children: <Widget>[
                  CreditCardForm(
                    formKey: formKey1,
                    obscureCvv: true,
                    obscureNumber: true,
                    cardNumber: cardNumber,
                    cvvCode: cvvCode,
                    isHolderNameVisible: true,
                    isCardNumberVisible: true,
                    isExpiryDateVisible: true,
                    cardHolderName: cardHolderName,
                    expiryDate: expiryDate,
                    themeColor: Colors.blue,
                    textColor: Colors.black,
                    cardNumberDecoration: InputDecoration(
                      labelText: 'Number',
                      hintText: 'XXXX XXXX XXXX XXXX',
                      hintStyle: const TextStyle(color: Colors.black),
                      labelStyle: const TextStyle(color: Colors.black),
                      focusedBorder: border,
                      enabledBorder: border,
                    ),
                    expiryDateDecoration: InputDecoration(
                      hintStyle: const TextStyle(color: Colors.black),
                      labelStyle: const TextStyle(color: Colors.black),
                      focusedBorder: border,
                      enabledBorder: border,
                      labelText: 'Expired Date',
                      hintText: 'XX/XX',
                    ),
                    cvvCodeDecoration: InputDecoration(
                      hintStyle: const TextStyle(color: Colors.black),
                      labelStyle: const TextStyle(color: Colors.black),
                      focusedBorder: border,
                      enabledBorder: border,
                      labelText: 'CVV',
                      hintText: 'XXX',
                      errorText: validateCVV(cvvCode),
                    ),
                    cardHolderDecoration: InputDecoration(
                      hintStyle: const TextStyle(color: Colors.black),
                      labelStyle: const TextStyle(color: Colors.black),
                      focusedBorder: border,
                      enabledBorder: border,
                      labelText: 'Card Holder',
                    ),
                    onCreditCardModelChange: onCreditCardModelChange,
                  ),
                  const SizedBox(
                    height: 10,
                  ),
                  Form(
                    key: formKey2,
                    child: Padding(
                      padding:
                          const EdgeInsets.fromLTRB(14.0, 12.0, 12.0, 12.0),
                      child: TextFormField(
                        keyboardType: TextInputType.number,
                        inputFormatters: [
                          FilteringTextInputFormatter.digitsOnly
                        ],
                        controller: amountController,
                        decoration: InputDecoration(
                          hintStyle: const TextStyle(color: Colors.black),
                          labelStyle: const TextStyle(color: Colors.black),
                          focusedBorder: border,
                          enabledBorder: border,
                          labelText: 'Amount',
                        ),
                        validator: (value) {
                          if (!(value!.isEmpty)) {
                            var Amount = int.parse(value);
                            if (Amount < 0) {
                              return 'Please enter valid amount';
                            } else if (Amount > 1000000) {
                              return 'The maximum amount of charging is 1000000';
                            }
                          } else {
                            return 'please enter amount';
                          }
                          return null;
                        },
                      ),
                    ),
                  ),
                  const SizedBox(
                    height: 10,
                  ),
                  ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8.0),
                      ),
                      primary: Colors.blue,
                    ),
                    child: Container(
                      margin: const EdgeInsets.all(10),
                      child: const Text(
                        'Charge',
                        style: TextStyle(
                          fontSize: 20,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                          package: 'flutter_credit_card',
                        ),
                      ),
                    ),
                    onPressed: () {
                      if (formKey1.currentState!.validate() &&
                          formKey2.currentState!.validate() &&
                          validCVV(cvvCode)) {
                        setState(() {
                          charging = ChargingWallet(_charging());
                        });
                      }
                    },
                  ),
                ],
              )))
    ]);
  }

  String? validateCVV(String cvv) {
    if (cvv.length > 3) {
      return 'please Enter only three digits';
    }
    return null;
  }

  bool validCVV(String cvv) {
    if (cvv.length == 3) return true;
    return false;
  }

  void onCreditCardModelChange(CreditCardModel? creditCardModel) {
    setState(() {
      cardNumber = creditCardModel!.cardNumber;
      expiryDate = creditCardModel.expiryDate;
      cardHolderName = creditCardModel.cardHolderName;
      cvvCode = creditCardModel.cvvCode;
      isCvvFocused = creditCardModel.isCvvFocused;
    });
  }

  FutureBuilder<chargingInfo> checkChargingResponse() {
    return FutureBuilder<chargingInfo>(
        future: charging,
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            if (snapshot.data!.status == true) {
              SchedulerBinding.instance.addPostFrameCallback((_) {
                //print('yes');
                Navigator.push(
                    context,
                    MaterialPageRoute(
                        builder: (_) => wallet(
                              email: email,
                              username: username,
                              rolename: rolename,
                            )));
                showDialog(
                    context: context,
                    builder: (BuildContext context) => AlertDialog(
                          title:
                              const Text('Your charging has been Successfully'),
                          content: Text('${snapshot.data!.message} EGY'),
                          actions: <Widget>[
                            TextButton(
                              onPressed: () => Navigator.pop(context, 'ok'),
                              child: const Text('Ok'),
                            ),
                          ],
                        ));
              });
            } else if (snapshot.data!.status == false) {
              SchedulerBinding.instance.addPostFrameCallback((_) {
                setState(() {
                  charging = null;
                  amountController.clear();
                  cvvCode = "";
                  cardNumber = "";
                  expiryDate = "";
                  cardHolderName = "";
                });
                showDialog(
                    context: context,
                    builder: (BuildContext context) => AlertDialog(
                          title: const Text('Your charging is failed'),
                          content: Text('${snapshot.data!.message}'),
                          actions: <Widget>[
                            TextButton(
                              onPressed: () => Navigator.pop(context, 'ok'),
                              child: const Text('Ok'),
                            ),
                          ],
                        ));
              });
            }
          }
          return Center(
            child: CircularProgressIndicator(color: Colors.blue),
            widthFactor: 25.0,
            heightFactor: 25.0,
          );
        });
  }
}
