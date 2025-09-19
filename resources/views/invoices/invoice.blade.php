<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
  <style>
    body {
      margin: 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border: none;
    }

    tbody tr:nth-child(even) {
      background: #FFFFFF;
    }

    th,
    td {
      padding: 10px;
      border: solid 1px #000000;
    }

    th {
      font-size: 12px;
      background-color: #a5a5a5;
      color: #ffffff;
      text-align: center;
    }

    td {
      font-size: 13px;
      text-align: center;
    }

    th.title,
    td.title {
      text-align: left;
    }

    td.hide-borders {
      border: none;
    }

    td.small {
      font-size: 11px;
      padding: 0px 10px;
      height: 16px;
      line-height: 16px;
      vertical-align: middle;
    }

    td.tax {
      padding: 4px 10px 0px;
    }

    .unit-price-et,
    .unit-price-et {
      width: 120px;
    }

    .quantity {
      width: 30px;
    }

    .item-price-et,
    .total-price-it {
      width: 120px;
    }

    .heading {
      font-weight: bold;
      text-align: center;
    }

    .price-amount .value,
    .price-amount .symbol {
      display: inline-block;
      padding: 0 1px;
    }

    .header {
      margin: 0 0 100px;
      overflow: hidden;
    }

    .header:after {
      content: "";
      display: table;
      clear: both;
    }

    .header .logo {
      text-align: center
    }

    .header .logo img {
      width: 160px;
    }

    .header .company-info {
      float: right;
      text-align: right;
      margin: 10px 0 0;
    }

    .company-details > div {
      margin: 0 0 6px;
      font-size: 14px;
    }

    .company-details > div:last-child {
      margin: 0;
    }

    .company-details .name {
      font-size: 16px;
      font-weight: bold;
    }

    .invoice-date {
      text-align: right;
      font-size: 14px;
      margin: 0 0 40px;
    }

    .invoice-number {
      text-align: center;
      font-size: 20px;
      font-weight: bold;
      margin: 0 0 60px;
      text-decoration: underline
    }

    .client-infos {
      margin: 0 0 30px;
      font-size: 14px;
    }

    .client-infos .client-info {
      margin: 0 0 6px;
    }

    .client-infos .client-info:last-child {
      margin: 0;
    }

    .client-info .label {
      font-size: 16px;
      font-weight: bold;
    }

    .client-info .value{
      font-size: 16px;
      font-weight: bold;
    }

    .taxes-notice-wrapper {
      text-align: right;
      margin: 30px 0;
    }

    .taxes-notice {
      font-size: 14px;
      border: solid 1px #000000;
      padding: 10px;
      display: inline-block;
    }

    .footer {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      text-align: center;
      line-height: 1;
      font-size: 13px;
    }

    .footer .line {
      margin: 0 0 5px;
    }

    .footer .line:last-child {
      margin: 0;
    }
  </style>
</head>
<body>
  <div class="header">
    <div class="logo">
      <img src="{{ $logo }}" alt="logo" />
    </div>
    <div class="company-info">
      <div class="company-details">
        {{-- <div class="name">{{ $settings['header_line_1'] }}</div>
        <div class="detail">{{ $settings['header_line_2'] }}</div>
        <div class="address">
          <div class="line1">{{ $settings['address_line_1'] }}</div>
          <div class="line2">{{ $settings['address_line_2'] }}</div>
        </div> --}}
      </div>
    </div>
  </div>
  <div class="invoice-date">
    <span class="name">Rabat, le </span>
    <span class="date">{{ $order->getFormattedUpdatedAt() }}</span>
  </div>
  <div class="invoice-number">
    <span class="name">Facture N° :</span>
    <span class="number">3240234023</span>
  </div>
  <div class="client-infos">
    <div class="client-info">
      <span class="label">Client :</span>
      <span class="value">
        {{ $order->getUser()->getName() }}
      </span>
    </div>
    @if ($order->getUser()->isCompany())
    <div class="client-info">
      <span class="label">ICE :</span>
      <span class="value">
        {{ $order->getUser()->getIce() }}
      </span>
    </div>
    @endif
  </div>
  <div class="details">
    <table>
      <thead>
        <tr>
          <th class="title">DESIGNATION</th>
          <th class="quantity">Quantité</th>
          <th class="unit-price-et">P.U (H.T)</th>
          <th class="item-price-et">Total H.T</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($order->getInvoiceItems() as $item)
        <tr>
          <td class="title">
            <div class="title-inner-wrapper">
              <span class="product-title">{{ $item['title'] }}</span>
            </div>
          </td>
          <td class="quantity">1</td>
          <td class="unit-price-et">{!! $item['price'] !!}</td>
          <td class="item-price-et">{!! $item['price'] !!}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td class="hide-borders"></td>
          <td class="hide-borders"></td>
          <td class="hide-borders heading">
            <span>TOTAL TTC</span>
          </td>
          <td class="total-price-it">{!! $order->getFormattedTotal() !!}</td>
        </tr>
      </tfoot>
    </table>
  </div>
  <div class="taxes-notice-wrapper">
    <div class="taxes-notice">
      <span class="text-1">Exonéré de la TVA</span>
      <span class="text-2">Conformément à l'article 91 (1-E)-1 du CGI</span>
    </div>
  </div>
  <div class="footer">
    <div class="line">Société NEWMEDIA PRODUCTION  SARL - RC: 156713. Patente 25108229. IF: 51640534. CNSS: 4104611.</div>
    <div class="line">ICE: 002973947000031. Banque Populaire Agence ALLAL BEN ABDALLAH. RIB: 022 810 0000500030351081 23</div>
    <div class="line">Capital social: 100000 MAD. N°6, 3ème étage 3, Rue Al Yanboua Hassan - Rabat. Tél: 0808621936. Email: contact@thevoice.ma</div>
  </div>
</body>
</html>
