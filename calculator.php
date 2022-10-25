<link rel="stylesheet" href="core.css">
<div class="block-calculator" style="padding-top:60px; padding-bottom:60px">
<div class="fusion-column-wrapper fusion-flex-justify-content-flex-start fusion-content-layout-column">
  <script src="/wp-content/scripts/progressBar.js"></script>
  
  <script>
    function decimalsales(entrees, decimals) {
      entrees += "";
      var original_entrees = entrees;
      var amount = parseFloat(entrees);
      var valpos = "";
      var valdec = "";
      if (isNaN(amount)) return original_entrees;
      if (decimals == 2) {
        amount = Math.round(100 * amount);
        var chaine;
        if (amount < 10) {
          chaine = "00" + amount;
        } else if (amount < 100) {
          chaine = "0" + amount;
        } else {
          chaine = "" + amount;
        }
        valpos = chaine.substring(0, chaine.length - 2);
        valdec = chaine.substring(chaine.length - 2, chaine.length);
      } else {
        valpos = "" + Math.round(amount);
      }
      var chiffresavant = "";
      var chiffresmilieu = "";
      if (valpos.length > 6) {
        chiffresavant = valpos.substring(0, (valpos.length - (parseInt(valpos.length / 3) * 3))) + ",";
      }
      if (valpos.length > 3) {
        chiffresmilieu = valpos.substring(valpos.length - 6, valpos.length - 3) + ",";
      }
      chiffresfin = valpos.substring(valpos.length - 3, valpos.length);
      new_pos = "$ " + chiffresavant + chiffresmilieu + chiffresfin;
      if (decimals == 2) {
        new_pos = new_pos + "." + valdec;
      }
      return new_pos;
    }

    function travailForm(quel) {
      var numpay = "" + document.mortgage_calculator.periodepaym.options[document.mortgage_calculator.periodepaym.selectedIndex].value;
      var termans = parseInt(document.mortgage_calculator.term.options[document.mortgage_calculator.term.selectedIndex].value);
      var periodesterm = termans * parseInt(numpay);
      var anneesamort = parseInt(document.mortgage_calculator.amortiss.options[document.mortgage_calculator.amortiss.selectedIndex].value);
      var periodtotals = anneesamort * parseInt(numpay);
      var valeurprina = document.mortgage_calculator.principal_calc.value;
      var calculepaym = document.mortgage_calculator.pay_period.value;
      var dp = parseFloat(document.mortgage_calculator.dpayment_calc.value);
      var tax = parseFloat(document.mortgage_calculator.interest_calc.value);
      while (calculepaym.indexOf("$") == 0 || calculepaym.indexOf(" ") == 0) {
        calculepaym = calculepaym.substring(1, calculepaym.length);
      }
      while (calculepaym.indexOf(",") != -1) {
        var calculepaym = "" + calculepaym.substring(0, calculepaym.indexOf(",")) + calculepaym.substring(calculepaym.indexOf(",") + 1, calculepaym.length)
      }
      while (valeurprina.indexOf("$") == 0 || valeurprina.indexOf(" ") == 0) {
        valeurprina = valeurprina.substring(1, valeurprina.length);
      }
      while (valeurprina.indexOf(",") != -1) {
        var valeurprina = "" + valeurprina.substring(0, valeurprina.indexOf(",")) + valeurprina.substring(valeurprina.indexOf(",") + 1, valeurprina.length)
      }
      var calcamount = parseFloat(calculepaym);
      if (calcamount != calculepaym && calculepaym > 0) {
        document.mortgage_calculator.pay_period.value = " ERROR ";
        return;
      }
      if (((document.mortgage_calculator.interest_calc.value == null || document.mortgage_calculator.interest_calc.value.length == 0) && quel != 'i') || ((document.mortgage_calculator.dpayment_calc.value == null || document.mortgage_calculator.dpayment_calc.value.length == 0) && quel != 'i') || ((document.mortgage_calculator.principal_calc.value == null || document.mortgage_calculator.principal_calc.value.length == 0) && quel != 'p')) {
        return;
      }
      diffcalc = 99999;
      intpayment = 0;
      taxcalc = 0.09;
      if (quel == "i" && calculepaym > 0) {
        while (Math.abs(diffcalc) > 0.1) {
          intpayment = (parseFloat(valeurprina) * taxcalc) / (1 - (1 / Math.pow((1 + taxcalc), periodtotals)));
          diffcalc = (calculepaym - intpayment);
          var sign = (diffcalc) / Math.abs(diffcalc)
          taxcalc = (0.01 * sign) * (Math.abs(diffcalc) > 500) + (0.0001 * sign) * (Math.abs(diffcalc) > 20) + (0.00001 * sign) * (Math.abs(diffcalc) > 10) + (0.000001 * sign) * (Math.abs(diffcalc) > 1) + (0.0000001 * sign) + taxcalc;
        }
        tax = 2 * (Math.pow((1 + taxcalc), parseInt(numpay) / 2) - 1);
        tax = parseInt(tax * 1000000) / 10000;
      }
      if (isNaN(tax)) { // Retourner chaine entrees si non convertible:
        alert('The interest_calc rate (' + tax + ') is causing an error!  Please re-enter values ...');
        document.mortgage_calculator.term_balance_ca.value = 0;
        document.mortgage_calculator.term_ca.value = 0;
        document.mortgage_calculator.payment_ca.value = 0;
        document.mortgage_calculator.intemprunttot_ca.value = 0;
        //document.mortgage_calculator.dpayment_calc.value  = 0;
        return;
      }
      if (tax < 0.3) {
        tax = tax * 100.0;
      }
      document.mortgage_calculator.interest_calc.value = tax + " %";
      tax = tax / 100.0;
      document.mortgage_calculator.dpayment_calc.value = dp + " %";
      var intcan = Math.pow((1 + tax / 2), (2 / parseInt(numpay))) - 1;
      var intcandebase = Math.pow((1 + tax / 2), (2 / 12)) - 1;
      var intus = tax / parseInt(numpay);
      var intusdebase = tax / 12;
      if (quel == "p" && intcan != 0) {
        calcpaymdebase = calculepaym;
        calcperiodes = parseInt(numpay);
        if ((numpay == '52a' || numpay == '26a') && intcan != 0) {
          var calcpaymdebase = calculepaym * (parseInt(numpay) / 13);
          var valeurprina = (calcpaymdebase / intcandebase) * (1 - (1 / (Math.pow((1 + intcandebase), (anneesamort * 12)))));
        } else {
          var valeurprina = (calculepaym / intcan) * (1 - (1 / (Math.pow((1 + intcan), (periodtotals)))));
        }
        document.mortgage_calculator.principal_calc.value = decimalsales(valeurprina, 0);
      }
      if (quel == "p" && intcan == 0) {
        var valeurprina = (calculepaym * periodtotals);
        document.mortgage_calculator.principal_calc.value = decimalsales(valeurprina, 0);
      }
      var amountprin = parseFloat(valeurprina);
      if (amountprin != valeurprina) {
        document.mortgage_calculator.principal_calc.value = " ERROR ";
        document.mortgage_calculator.pay_period.value = " ERROR ";
        return;
      }
      if (anneesamort < termans) {
        alert('The Amortization (' + anneesamort + ')  must be greater than the term (' + termans + ') !  Please re-enter values ...');
        document.mortgage_calculator.term_balance_ca.value = 0;
        document.mortgage_calculator.term_ca.value = 0;
        document.mortgage_calculator.payment_ca.value = 0;
        //document.mortgage_calculator.dp_ca.value = 0; 
        document.mortgage_calculator.intemprunttot_ca.value = 0;
        return;
      }
      var paymbasecana = (amountprin * intcandebase) / (1 - (1 / Math.pow((1 + intcandebase), (anneesamort * 12))));
      var paymcana = paymbasecana;
      if ((numpay == '52a' || numpay == '26a') && intcan != 0) {
        var paymcana = paymbasecana / (parseInt(numpay) / 13);
        var paymusa = paymbaseusa / (parseInt(numpay) / 13);
      }
      if ((numpay == '52' || numpay == '26' || numpay == '2' || numpay == '1') && intcan != 0) {
        var paymcana = (amountprin * intcan) / (1 - (1 / Math.pow((1 + intcan), periodtotals)));
      }
      document.mortgage_calculator.pay_period.value = decimalsales(paymcana, 2);
      document.mortgage_calculator.payment_ca.value = decimalsales(paymcana, 2);
      //document.mortgage_calculator.ddp.value = decimalsales(paymcana,2); 
      var balcana = valeurprina;
      var intcana = 0;
      var inttotcana = 0;
      for (var jj = 0; jj < periodesterm; jj++) {
        intcana = intcan * balcana;
        inttotcana = inttotcana + intcana;
        balcana = balcana - (paymcana - intcana);
        if (balcana < 0) {
          balcana = 0
          break;
        }
      }
      dp_value = dp;
      dp_sum = ((valeurprina * dp_value) / 100);
      faa = Math.round(dp_sum);
      dp_principal = valeurprina - faa;
      dp_sum = decimalsales(faa, 0);
      final_principal = valeurprina - faa;
      if (balcana < 0) {
        balcana = 0
      };
      document.mortgage_calculator.term_balance_ca.value = decimalsales(parseInt(balcana), 0);
      document.mortgage_calculator.term_ca.value = decimalsales(parseInt(inttotcana), 0);
      document.mortgage_calculator.term_pay_ca.value = decimalsales(faa - (parseInt(balcana)), 0);
      //alert (dp_value);
      var kk = jj;
      for (var jj = periodesterm; jj < periodtotals; jj++) {
        intcana = intcan * balcana;
        inttotcana = inttotcana + intcana;
        balcana = balcana - (paymcana - intcana);
        kk = jj;
        if (balcana < 0) {
          balcana = 0
          break;
        }
      }
      vraiterm = decimalsales((kk + 1) / parseInt(numpay), 2);
      if (intcan == 0) {
        vraiterm = anneesamort;
      } else {
        vraiterm = vraiterm.substring(1, vraiterm.length);
      }
      document.mortgage_calculator.amtz_actual_can.value = "" + vraiterm + " yrs";
      document.mortgage_calculator.intemprunttot_ca.value = decimalsales(parseInt(inttotcana), 0);
      document.mortgage_calculator.principal_calc.value = decimalsales(amountprin, 0);
      final_principal = decimalsales(final_principal, 0);
      cc(valeurprina, paymcana);
    }
  </script>
  <script>
    function cc(a, b) {
      bar_perc = (faa / dp_principal);
      if (dp_value > 50) {
        bar_perc = 1;
      }
      bar.animate(bar_perc);
      document.getElementById("dp_sum").innerHTML = final_principal;
      document.getElementById("subtotal_sum").innerHTML = dp_sum;
    }
  </script>
  <form name="mortgage_calculator" class="mortgage_calculator">
    <center>
      <table>
        <tbody>
          <tr>
            <td>
              <div>
                <table border="0">
                  <tbody>
                    <tr>
                      <td>
                        <table border="0">
                          <tbody>
                            <tr>
                              <td class="values_wrapper">
                                <h3>Mortgage Calculator</h3>
                              </td>
                              <td rowspan="8" class="divider"></td>
                              <td rowspan="7" class="result_wrapper">
                                <!--result-->
                                <div class="result_bg">
                                  <div>
                                    <label>Periodic Payment</label>
                                  </div>
                                  <input name="payment_ca" type="text" id="payment_ca" size="12" value="$ 0" disabled>
                                </div>
                                <div class="progressbar_values">
 								  <img class="graydot" src="/wp-content/uploads/sites/49/2022/02/icon_mortgage-dot-gray.png" /><div id="subtotal_sum"></div>
							      <img class="blackdot" src="/wp-content/uploads/sites/49/2022/02/icon_mortgage-dot-black.png" /><div id="dp_sum">$ 0</div>
                                 
                                </div>
                                <div id="containermortg" style="position: absolute;"></div>
                                <div id="bgmortg" style="position: absolute; float: left;z-index: -1;"></div>
                              
<div class="calc-disclaimer">* For informational purposes only</div></td>
                            </tr>
                            <tr>
                              <td>
                                <div class="calc-label-wrapper">
                                  <label>Asking Price</label>
                                </div>
                                <input type="text" name="principal_calc" size="12" value="$ 100,000">
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <div class="calc-label-wrapper">
                                  <label>Interest Rate</label>
                                </div>
                                <input type="text" name="interest_calc" size="12" value="6.25 %">
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <div class="calc-label-wrapper">
                                  <label>Downpayment %</label>
                                </div>
                                <input type="text" name="dpayment_calc" size="12" value="20 %">
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <select name="term" size="1" style="display:none">
                                  <option value="1" selected=""> 1 </option>
                                  <option value="2"> 2 </option>
                                  <option value="3"> 3 </option>
                                  <option value="4"> 4 </option>
                                  <option value="5"> 5 </option>
                                  <option value="6"> 6 </option>
                                  <option value="7"> 7 </option>
                                  <option value="8"> 8 </option>
                                  <option value="9"> 9 </option>
                                  <option value="10"> 10 </option>
                                  <option value="11"> 11 </option>
                                  <option value="12"> 12 </option>
                                  <option value="13"> 13 </option>
                                  <option value="14"> 14 </option>
                                  <option value="15"> 15 </option>
                                  <option value="16"> 16 </option>
                                  <option value="17"> 17 </option>
                                  <option value="18"> 18 </option>
                                  <option value="19"> 19 </option>
                                  <option value="20"> 20 </option>
                                  <option value="21"> 21 </option>
                                  <option value="22"> 22 </option>
                                  <option value="23"> 23 </option>
                                  <option value="24"> 24 </option>
                                  <option value="25"> 25 </option>
                                  <option value="26"> 26 </option>
                                  <option value="27"> 27 </option>
                                  <option value="28"> 28 </option>
                                  <option value="29"> 29 </option>
                                  <option value="30"> 30 </option>
                                </select>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <div class="calc-label-wrapper">
                                  <label>Amortization (Years)</label>
                                </div>
                                <select name="amortiss" size="1">
                                  <option value="1"> 1 </option>
                                  <option value="2"> 2 </option>
                                  <option value="3"> 3 </option>
                                  <option value="4"> 4 </option>
                                  <option value="5" selected=""> 5 </option>
                                  <option value="6"> 6 </option>
                                  <option value="7"> 7 </option>
                                  <option value="8"> 8 </option>
                                  <option value="9"> 9 </option>
                                  <option value="10"> 10 </option>
                                  <option value="11"> 11 </option>
                                  <option value="12"> 12 </option>
                                  <option value="13"> 13 </option>
                                  <option value="14"> 14 </option>
                                  <option value="15"> 15 </option>
                                  <option value="16"> 16 </option>
                                  <option value="17"> 17 </option>
                                  <option value="18"> 18 </option>
                                  <option value="19"> 19 </option>
                                  <option value="20"> 20 </option>
                                  <option value="21"> 21 </option>
                                  <option value="22"> 22 </option>
                                  <option value="23"> 23 </option>
                                  <option value="24"> 24 </option>
                                  <option value="25"> 25 </option>
                                  <option value="26"> 26 </option>
                                  <option value="27"> 27 </option>
                                  <option value="28"> 28 </option>
                                  <option value="29"> 29 </option>
                                  <option value="30"> 30 </option>
                                </select>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <div class="calc-label-wrapper">
                                  <label>Payment type</label>
                                </div>
                                <select name="periodepaym" size="1">
                                  <option value="1"> Annually </option>
                                  <option value="2"> Semi-Annually </option>
                                  <option value="12" selected=""> Monthly </option>
                                  <option value="26"> Bi-Weekly </option>
                                  <option value="52"> Weekly </option>
                                </select>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <input class="calc" name="button2" type="button" onclick="travailForm()" value="Calculate">
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <input name="term_ca" type="text" id="term_ca" size="12" hidden="0">
                        <input name="intemprunttot_ca" type="text" id="intemprunttot_ca" size="12" hidden="0">
                        <input name="term_pay_ca" type="text" id="term_pay_ca" size="12" hidden="0">
                        <input name="amtz_actual_can" type="text" id="amtz_actual_can" size="12" hidden="0">
                        <input name="term_balance_ca" type="text" id="term_balance_ca" size="12" hidden="0">
                        <input name="pay_period" type="text" value="$ 654.74" size="12" hidden="0">
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </center>
  </form>
  <script>
    //document.getElementById("dp_sum").innerHTML = document.mortgage_calculator.payment_dp.value;
    document.getElementById("subtotal_sum").innerHTML = document.mortgage_calculator.payment_ca.value;
    var bar = new ProgressBar.Line(containermortg, {
      strokeWidth: 4,
      easing: 'easeInOut',
      duration: 1400,
      color: '#63717d',
      trailColor: 'none',
      trailWidth: 0
    });
  </script>
  </div>
  </div>
  