<!DOCTYPE html>
<?php 
session_start(); 
include_once('lib/auth.php');
$auth_email = auth();
if (!$auth_email) {
  // false, fake/no cookie
  $auth_email = "GUEST";
}
?>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body>
    <input id="hid_currentUser" value="<?php echo htmlspecialchars($auth_email)?>" type="hidden"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Replace "test" with your own sandbox Business account app client ID -->
    <script src="https://www.paypal.com/sdk/js?client-id=AUqbIitXVo-JFfxcA4EVAkq4dxevLk998scM5r3cKpI_Wov_6-Stxh9XIeOY1t9PNmeaZb8dDLR_pDy-&currency=HKD"></script>
    <!-- Set up a container element for the button -->
    <div id="paypal-button-container"></div>
    <script>
      const merchEmail = "sb-agssq15570287@business.example.com";
      function isNumeric(value) {
          return /^\d+$/.test(value);
      }

      let keys_i = Object.keys(localStorage), i_i = keys_i.length, count_i = 0;
      while (i_i--) {
        if (isNumeric(keys_i[i_i])) {
          count_i++;
        }
      }
      if (count_i <= 0) {
        alert(`You need at least 1 item to checkout.`);
        window.location.replace('https://secure.s13.ierg4210.ie.cuhk.edu.hk/index.php');
      }



      function getItemsInfo(itemID_s) {
        return new Promise((resolve) => {
          $.ajax({
            type: 'post',
            url: '/lib/checkout_items.php',
            data: {
              itemID: itemID_s
            },
            success: function(response) {
              resolve(response)
            }
          });
        })
      }

      function uploadOrderToServer(result_s, email) {
        return new Promise((resolve) => {
          $.ajax({
            type: 'post',
            url: '/lib/upload_order.php',
            data: {
              orderList: result_s,
              userEmail: email
            },
            success: function(response) {
              // return digest +" "+ orderID
              resolve(response);
            }
          });
        })
      }

      async function obtainObjectFromLocalStorage() {
        let result = {};
        let archive = {                
          //custom_id: "aabbccddeeff",  /* digest */
          //invoice_id: "001122334455", /* lastInsertId() */
        }, keys = Object.keys(localStorage), i = keys.length;
        archive["amount"] = {currency_code: 'HKD'};
        archive["items"] = [];
        totalValue = 0;
        while (i--) {
          if (isNumeric(keys[i])) {
            // a product, create object {name: "", unit_amount:{currency_code:"", value:""}, quantity: int}
            await getItemsInfo(keys[i]).then((data)=> {
              let prodObj = {};
              dataSeg = data.split('~');
              prodObj["name"] = dataSeg[0];
              prodObj["quantity"] = parseInt(localStorage.getItem(keys[i]));
              prodObj["unit_amount"] = {
                currency_code: 'HKD',
                value: parseFloat(dataSeg[1])
              }
              totalValue += parseInt(localStorage.getItem(keys[i])) * parseFloat(dataSeg[1]);
              archive["items"].push(prodObj);
            })
            
          }
        }
        archive["amount"]["value"] = totalValue;
        archive["amount"]["breakdown"] = {item_total: {currency_code: 'HKD', value: totalValue}};

        result["purchase_units"] = [];
        result["purchase_units"].push(archive);
        // create digest
        let userEmail = document.querySelector("#hid_currentUser").value;
        let result_s = JSON.stringify(result);
        // upload to sql (with digest)
        await uploadOrderToServer(result_s, userEmail).then((data) => {
          // append digest to JSON
          dataSeg = data.split(" ");
          // append orderID to to JSON
          result["purchase_units"][0]["custom_id"] = dataSeg[0];
          result["purchase_units"][0]["invoice_id"] = dataSeg[1];
          
        })
        //console.log(archive);
        return result;
      }

      //let preJsonObj = {};
      function getFromServer(preJsonObj) {
        return new Promise(resolve => {
          setTimeout(() => {
            resolve(JSON.stringify(preJsonObj));
          }, 100);
        });
      }


      paypal.Buttons({
        /* Sets up the transaction when a payment button is clicked */
        createOrder: async (data, actions) => { /* async is required to use await in a function */
          /* Use AJAX to get required data from the server; For dev/demo purposes: */
          let preJsonObj = {};
          await obtainObjectFromLocalStorage().then(data => {
            Object.assign(preJsonObj, data);
            //console.log(preJsonObj);
          })
          let order_details = await getFromServer(preJsonObj)
            .then(data => JSON.parse(data));

          /* Use fetch() instead in real code to get server resources */
          // let order_details = await fetch(/* resource url*/)
          //     .then(response => response.json()) /* json string to javascript object */
          //     .then(data => {
          //         /* process over data */
          //         return /* return value */;
          //     });

          return actions.order.create(order_details);
        },

        // Finalize the transaction after payer approval
        onApprove: (data, actions) => {
          return actions.order.capture().then(function(orderData) {
            // Successful capture! For dev/demo purposes:
            //console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
            const transaction = orderData.purchase_units[0].payments.captures[0];
            //alert(`Transaction ${transaction.status}: ${transaction.id}\n\nSee console for all available details`);
            
            // When ready to go live, remove the alert and show a success message within this page. For example:
            // const element = document.getElementById('paypal-button-container');
            // element.innerHTML = '<h3>Thank you for your payment!</h3>';
            // Or go to another URL:  actions.redirect('thank_you.html');

            // remember to clear storage
            let paypal_reserve = localStorage.getItem('__paypal_storage__');
            localStorage.clear();
            localStorage.setItem('__paypal_storage__',paypal_reserve);
            window.location.replace('https://secure.s13.ierg4210.ie.cuhk.edu.hk/payment-success.html');
          });
        }
      }).render('#paypal-button-container');
    </script>
  </body>
</html>