<!DOCTYPE html>
<?php session_start(); ?>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body>
    <!-- Replace "test" with your own sandbox Business account app client ID -->
    <script src="https://www.paypal.com/sdk/js?client-id=AUqbIitXVo-JFfxcA4EVAkq4dxevLk998scM5r3cKpI_Wov_6-Stxh9XIeOY1t9PNmeaZb8dDLR_pDy-&currency=HKD"></script>
    <!-- Set up a container element for the button -->
    <div id="paypal-button-container"></div>
    <script>
      paypal.Buttons({
        // Sets up the transaction when a payment button is clicked
        createOrder: (data, actions) => {
            return actions.order.create({
                "purchase_units": [{
                    "amount": {
                        "currency_code": "HKD",
                        "value": "10",
                        "breakdown": {
                            "item_total": {  /* Required when including the `items` array */
                            "currency_code": "HKD",
                            "value": "10"
                            }
                        }
                    },
                    "items": [
                    {
                        "name": "First Product Name", /* Shows within upper-right dropdown during payment approval */
                        "unit_amount": {
                            "currency_code": "HKD",
                            "value": "5"
                        },
                        "quantity": "2"
                    },
                    ]
                }]
            });
        },

        // Finalize the transaction after payer approval
        onApprove: (data, actions) => {
          return actions.order.capture().then(function(orderData) {
            // Successful capture! For dev/demo purposes:
            console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
            const transaction = orderData.purchase_units[0].payments.captures[0];
            alert(`Transaction ${transaction.status}: ${transaction.id}\n\nSee console for all available details`);
            // When ready to go live, remove the alert and show a success message within this page. For example:
            // const element = document.getElementById('paypal-button-container');
            // element.innerHTML = '<h3>Thank you for your payment!</h3>';
            // Or go to another URL:  actions.redirect('thank_you.html');
          });
        }
      }).render('#paypal-button-container');
    </script>
  </body>
</html>