<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:fn="http://www.w3.org/2005/xpath-functions" xmlns:gl-bus="http://www.xbrl.org/int/gl/bus/2006-10-25" xmlns:gl-cor="http://www.xbrl.org/int/gl/cor/2006-10-25" xmlns:gl-gen="http://www.xbrl.org/int/gl/gen/2006-10-25" xmlns:iso4217="http://www.xbrl.org/2003/iso4217" xmlns:link="http://www.xbrl.org/2003/linkbase" xmlns:xades="http://uri.etsi.org/01903/v1.3.2#" xmlns:xbrli="http://www.xbrl.org/2003/instance" xmlns:xl="http://www.xbrl.org/2003/XLink" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:edefter="http://www.edefter.gov.tr">
	<xsl:character-map name="myMap">
		<xsl:output-character character="&#128;" string=""/>
		<xsl:output-character character="&#129;" string=""/>
		<xsl:output-character character="&#130;" string=""/>
		<xsl:output-character character="&#131;" string=""/>
		<xsl:output-character character="&#132;" string=""/>
		<xsl:output-character character="&#133;" string=""/>
		<xsl:output-character character="&#134;" string=""/>
		<xsl:output-character character="&#135;" string=""/>
		<xsl:output-character character="&#136;" string=""/>
		<xsl:output-character character="&#137;" string=""/>
		<xsl:output-character character="&#138;" string=""/>
		<xsl:output-character character="&#139;" string=""/>
		<xsl:output-character character="&#140;" string=""/>
		<xsl:output-character character="&#141;" string=""/>
		<xsl:output-character character="&#142;" string=""/>
		<xsl:output-character character="&#143;" string=""/>
		<xsl:output-character character="&#144;" string=""/>
		<xsl:output-character character="&#145;" string=""/>
		<xsl:output-character character="&#146;" string=""/>
		<xsl:output-character character="&#147;" string=""/>
		<xsl:output-character character="&#148;" string=""/>
		<xsl:output-character character="&#149;" string=""/>
		<xsl:output-character character="&#150;" string=""/>
		<xsl:output-character character="&#151;" string=""/>
		<xsl:output-character character="&#152;" string=""/>
		<xsl:output-character character="&#153;" string=""/>
		<xsl:output-character character="&#154;" string=""/>
		<xsl:output-character character="&#155;" string=""/>
		<xsl:output-character character="&#156;" string=""/>
		<xsl:output-character character="&#157;" string=""/>
		<xsl:output-character character="&#158;" string=""/>
		<xsl:output-character character="&#159;" string=""/>
	</xsl:character-map>
	<xsl:output version="4.0" method="html" indent="no" encoding="UTF-8" doctype-public="-//W3C//DTD HTML 4.01 Transitional//EN" doctype-system="http://www.w3.org/TR/html4/loose.dtd" use-character-maps="myMap"/>
	<xsl:decimal-format name="tryFormat" grouping-separator="." decimal-separator=","/>
	<xsl:variable name="VKN_TCKN" select="/edefter:defter/xbrli:xbrl[1]/xbrli:context[1]/xbrli:entity[1]"/>
	<xsl:variable name="UNVAN" select="/edefter:defter/xbrli:xbrl[1]/gl-cor:accountingEntries[1]/gl-cor:entityInformation/gl-bus:organizationIdentifiers[gl-bus:organizationDescription = 'Kurum Unvanı' ]/gl-bus:organizationIdentifier"/>
	<xsl:variable name="AD_SOYAD" select="/edefter:defter/xbrli:xbrl[1]/gl-cor:accountingEntries[1]/gl-cor:entityInformation/gl-bus:organizationIdentifiers[gl-bus:organizationDescription = 'Adı Soyadı' ]/gl-bus:organizationIdentifier"/>
	<xsl:variable name="SUBE_ADI" select="/edefter:defter/xbrli:xbrl[1]/gl-cor:accountingEntries[1]/gl-cor:entityInformation/gl-bus:organizationIdentifiers[gl-bus:organizationDescription = 'Şube Adı' ]/gl-bus:organizationIdentifier"/>
	<xsl:template match="/edefter:defter">
		<html>
			<head>
				<style type="text/css">
					body.bodyClass{
						font-family:Arial Narrow;
						font-size:10pt;
					}

					table.pageHeader1{
						border:0;
						cellpadding:2;
						cellspacing:5;
						width:100%;
						font-weight:bold;
					}

					table.pageHeader2{
						border:0;
						width:100%;
						border-spacing: 6px;
					}

					table.pageHeader2 tbody tr{
						border-bottom-style:none;
					}

					table.pageHeader2 tbody tr:nth-child(1) td{
						border-bottom-color:black;
						border-bottom-style:dashed;
						border-bottom-width:thin;
						overflow:inherit;
						padding-bottom:2px;
						text-align:center;
						font-weight:bold;
					}

					table.pageHeader2 tbody tr:nth-child(2) td{
						border-top-color:black;
						border-top-style:dashed;
						border-top-width:thin;
						overflow:inherit;
					}

					table.entryHeaderHeader {
						width:100%;
						font-weight:bold;
						margin-bottom:20px;
					}

					table.entryHeaderHeader tr td:nth-child(1){
						width:40%;
						padding-left:5px;
					}

					table.entryHeaderHeader tr td:nth-child(2){
						width:45%;
						padding-left:5px;
					}

					table.entryHeaderHeader tr td:nth-child(3){
						width:300px;
					}

					table.entryHeaderHeader tr td div:nth-child(2){
						border-bottom-style:dashed;
						border-bottom-width:2px;
						border-color:black;
						font-size:1px;
						margin-top:-8px;
						margin-left:80px;
						margin-right:10px;
					}

					table.entryDetail{
						border:0;
						overflow:hidden;
						width:100%;
						margin-top:5px;
						cellpadding:0;
						cellspacing:2;
					}

					table.entryDetail tr{
						height:0.2in;
					}

					div.entryHeaderFooter{
						margin-bottom:15px;
						text-align:center;
					}

				</style>
			</head>
			<body class="bodyClass">
				<xsl:apply-templates select="xbrli:xbrl"/>
			</body>
		</html>
	</xsl:template>
	<!-- xbrli:xbrl -->
	<xsl:template match="xbrli:xbrl">
		<xsl:apply-templates select="gl-cor:accountingEntries"/>
	</xsl:template>
	<!-- gl-cor:accountingEntries -->
	<xsl:template match="gl-cor:accountingEntries">
		<xsl:apply-templates select="gl-cor:entityInformation"/>
		<xsl:apply-templates select="gl-cor:documentInfo"/>
		<xsl:call-template name="printPageHeader"/>
		<xsl:apply-templates select="gl-cor:entryHeader"/>
		<xsl:call-template name="printPageFooter"/>
	</xsl:template>
	<!-- gl-cor:entityInformation -->
	<xsl:template match="gl-cor:entityInformation">
		<table style="width:100%; font-size:14px; font-weight:bold; margin-top:40px;">
			<tbody>
				<tr>
					<td style="text-align:left; padding-bottom:20px;">
						<span>
							<xsl:if test="$UNVAN != '' ">
								<xsl:text>Kurum Unvanı: </xsl:text>
								<xsl:value-of select="$UNVAN"/>
							</xsl:if>
							<xsl:if test="$AD_SOYAD != '' ">
								<xsl:text>Adı Soyadı: </xsl:text>
								<xsl:value-of select="$AD_SOYAD"/>
							</xsl:if>
						</span>
					</td>
					<td style="text-align:right; font-size:16px;">
						<span>
							<xsl:text>YEVMİYE DEFTERİ</xsl:text>
						</span>
					</td>
				</tr>
				<xsl:if test="$SUBE_ADI != '' ">
					<tr>
						<td style="text-align:left; padding-bottom:20px;">
							<span>
								<xsl:text>Şube Adı: </xsl:text>
								<xsl:value-of select="$SUBE_ADI"/>
							</span>
						</td>
					</tr>
				</xsl:if>
				<tr>
					<td style="text-align:left; padding-top:10px;">
						<span>
							<xsl:if test="$UNVAN != '' ">
								<xsl:text>Vergi No: </xsl:text>
							</xsl:if>
							<xsl:if test="$AD_SOYAD != '' ">
								<xsl:text>TC Kimlik No: </xsl:text>
							</xsl:if>
							<xsl:value-of select="$VKN_TCKN"/>
						</span>
					</td>
				</tr>
			</tbody>
		</table>
	</xsl:template>
	<!-- gl-cor:documentInfo -->
	<xsl:template match="gl-cor:documentInfo">
		<table style="width:100%; font-size:14px; font-weight:bold; margin-top:10px; margin-bottom:30px;">
			<tbody>
				<tr>
					<td style="text-align:center; padding-top:10px;">
						<span>
							<xsl:value-of select="gl-cor:entriesComment"/>
						</span>
					</td>
				</tr>
			</tbody>
		</table>
	</xsl:template>
	<!-- printPageHeader -->
	<xsl:template name="printPageHeader">
		<table class="pageHeader1">
			<tbody>
				<tr>
					<td style="width:40%;">
						<span>
							<xsl:text>Yevmiye Madde No</xsl:text>
						</span>
					</td>
					<td style="width:40%;">
						<span>
							<xsl:text>Yevmiye Tarihi</xsl:text>
						</span>
					</td>
					<td style="width:300px; "/>
				</tr>
			</tbody>
		</table>
		<table class="pageHeader2">
			<tbody>
				<tr>
					<td style="width:155px;">
						<span>
							<xsl:text>Hesap Kodu</xsl:text>
						</span>
					</td>
					<td style="width:2.28in;">
						<span>
							<xsl:text>Hesap Adı</xsl:text>
						</span>
						<br/>
					</td>
					<td style="width:2.97in;">
						<span>
							<xsl:text>Açıklama</xsl:text>
						</span>
					</td>
					<td style="width:70px; ">
						<span>
							<xsl:text>Borç </xsl:text>
						</span>
					</td>
					<td style="width:65px; ">
						<span>
							<xsl:text>Alacak</xsl:text>
						</span>
					</td>
				</tr>
				<tr>
					<td style="width:155px; ">
						<span>
							<xsl:text>&#160;</xsl:text>
						</span>
					</td>
					<td style="width:2.28in; ">
						<span>
							<xsl:text>&#160;</xsl:text>
						</span>
					</td>
					<td style="width:2.97in; ">
						<span>
							<xsl:text>&#160;</xsl:text>
						</span>
					</td>
					<td style="width:70px; ">
						<span>
							<xsl:text>&#160;</xsl:text>
						</span>
					</td>
					<td style="width:65px; ">
						<span>
							<xsl:text>&#160;</xsl:text>
						</span>
					</td>
				</tr>
			</tbody>
		</table>
	</xsl:template>
	<!-- printPageFooter -->
	<xsl:template name="printPageFooter">
		<hr width="100%" style="border: 1px dashed black;" color="#FFFFFF" size="6"/>
		<table style="border:0; font-style:normal; width:100%; margin-bottom:50px; " border="0" cellpadding="0" cellspacing="0" width="100%">
			<tbody>
				<tr style="font-style:normal; height:0.01in; ">
					<td style="width:50in; "/>
					<td style="text-align:right; width:10in; ">
						<span style="font-weight:bold; ">
							<xsl:text>Borç Toplamı :</xsl:text>
						</span>
					</td>
					<td style="width:11in; "/>
					<td style="text-align:right; width:5in; ">
						<span style="font-weight:bold; ">
							<xsl:variable name="debitSum">
								<xsl:value-of select="format-number(number(sum(gl-cor:entryHeader/gl-bus:totalCredit)), '###.##0,00', 'tryFormat')"/>
							</xsl:variable>
							<xsl:value-of select="$debitSum"/>
						</span>
					</td>
					<td style="width:5in; "/>
				</tr>
				<tr style="font-style:normal; height:0.01in; ">
					<td style="width:50in;"/>
					<td style="width:10in;"/>
					<td style="text-align:right; width:11in; ">
						<span style="font-weight:bold; ">
							<xsl:text>Alacak Toplamı : </xsl:text>
						</span>
					</td>
					<td style="width:5in; "/>
					<td style="text-align:right; width:5in; ">
						<span style="font-weight:bold; ">
							<xsl:variable name="creditSum">
								<xsl:value-of select="format-number(number(sum(gl-cor:entryHeader/gl-bus:totalDebit)), '###.##0,00', 'tryFormat')"/>
							</xsl:variable>
							<xsl:value-of select="$creditSum"/>
						</span>
					</td>
				</tr>
			</tbody>
		</table>
	</xsl:template>
	<!-- gl-cor:entryHeader -->
	<xsl:template match="gl-cor:entryHeader">
		<xsl:call-template name="printEntryHeaderHeader"/>
		<xsl:apply-templates select="gl-cor:entryDetail"/>
		<xsl:call-template name="printEntryHeaderFooter"/>
	</xsl:template>
	<!-- printEntryHeaderHeader -->
	<xsl:template name="printEntryHeaderHeader">
		<table class="entryHeaderHeader">
			<tr>
				<td>
					<div>
						<xsl:text>[ </xsl:text>
						<xsl:value-of select="gl-cor:entryNumberCounter"/>
						<xsl:text> ]</xsl:text>
					</div>
					<div/>
				</td>
				<td>
					<div>
						<xsl:text>[ </xsl:text>
						<xsl:call-template name="convertDate">
							<xsl:with-param name="date" select="gl-cor:enteredDate"/>
						</xsl:call-template>
						<xsl:text> ]</xsl:text>
					</div>
					<div/>
				</td>
				<td/>
			</tr>
		</table>
	</xsl:template>
	<!-- gl-cor:entryDetail -->
	<xsl:template match="gl-cor:entryDetail">
		<xsl:variable name="debitCreditNote" select="normalize-space(gl-cor:debitCreditCode)"/>
		<xsl:variable name="amount" select="format-number(number(gl-cor:amount), '###.##0,00', 'tryFormat')"/>
		<xsl:variable name="documentType" select="normalize-space(gl-cor:documentType)"/>
		<table class="entryDetail">
			<!-- print main acoount information -->
			<tr>
				<xsl:choose>
					<xsl:when test="$debitCreditNote = 'C' or $debitCreditNote = 'credit'">
						<td style="width:30px;"/>
					</xsl:when>
				</xsl:choose>
				<td style="width:150px; font-weight:bold; padding-left:10px;">
					<xsl:value-of select="gl-cor:account/gl-cor:accountMainID"/>
					<xsl:text>&#160;</xsl:text>
				</td>
				<td style="font-weight:bold;">
					<xsl:value-of select="gl-cor:account/gl-cor:accountMainDescription"/>
					<xsl:text>&#160;</xsl:text>
				</td>
				<td style="width:100px; text-align:right; padding-right:20px;">
					<xsl:choose>
						<xsl:when test="$debitCreditNote = 'D' or $debitCreditNote = 'debit'">
							<xsl:value-of select="$amount"/>
						</xsl:when>
					</xsl:choose>
				</td>
				<td style="width:100px; text-align:right; padding-right:20px;">
					<xsl:choose>
						<xsl:when test="$debitCreditNote = 'C' or $debitCreditNote = 'credit'">
							<xsl:value-of select="$amount"/>
						</xsl:when>
					</xsl:choose>
				</td>
			</tr>
			<!-- print sub account information -->
			<xsl:choose>
				<xsl:when test="gl-cor:account/gl-cor:accountSub/gl-cor:accountSubID">
					<tr>
						<xsl:choose>
							<xsl:when test="$debitCreditNote = 'C' or $debitCreditNote = 'credit'">
								<td style="width:30px;"/>
							</xsl:when>
						</xsl:choose>
						<td style="width:150px; padding-left:10px;">
							<xsl:value-of select="gl-cor:account/gl-cor:accountSub/gl-cor:accountSubID"/>
							<xsl:text>&#160;</xsl:text>
						</td>
						<td>
							<xsl:value-of select="gl-cor:account/gl-cor:accountSub/gl-cor:accountSubDescription"/>
							<xsl:text>&#160;</xsl:text>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<!-- print payment method -->
			<xsl:choose>
				<xsl:when test="string-length(normalize-space(gl-bus:paymentMethod)) != 0 ">
					<tr style="font-size:8pt; font-style:italic;">
						<xsl:choose>
							<xsl:when test="$debitCreditNote = 'C' or $debitCreditNote = 'credit'">
								<td style="width:30px;"/>
							</xsl:when>
						</xsl:choose>
						<td style="width:150px;"/>
						<td>
							<span style="font-weight:bold;">Ödeme Şekli : </span>
							<xsl:value-of select="gl-bus:paymentMethod"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<!-- print measurable -->
			<xsl:choose>
				<xsl:when test="gl-bus:measurable">
					<tr style="font-size:8pt; font-style:italic;">
						<xsl:choose>
							<xsl:when test="$debitCreditNote = 'C' or $debitCreditNote = 'credit'">
								<td style="width:30px;"/>
							</xsl:when>
						</xsl:choose>
						<td style="width:150px;"/>
						<td>
							<span style="font-weight:bold;">
								<xsl:value-of select="gl-bus:measurable/gl-bus:measurableQualifier"/>
								<xsl:text disable-output-escaping="yes">
								</xsl:text>
								<xsl:value-of select="gl-bus:measurable/gl-bus:measurableUnitOfMeasure"/>:</span>
							<xsl:value-of select="gl-bus:measurable/gl-bus:measurableQuantity"/>
							<span style="font-weight:bold;">Birim Fiyat: </span>
							<xsl:value-of select="gl-bus:measurable/gl-bus:measurableCostPerUnit"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<!-- print document type, document number, document date -->
			<xsl:choose>
				<xsl:when test="string-length($documentType) != 0">
					<xsl:variable name="documentNumber" select="normalize-space(gl-cor:documentNumber)"/>
					<xsl:variable name="documentDate" select="normalize-space(gl-cor:documentDate)"/>
					<tr style="font-size:8pt; font-style:italic;">
						<xsl:choose>
							<xsl:when test="$debitCreditNote = 'C' or $debitCreditNote = 'credit'">
								<td style="width:30px;"/>
							</xsl:when>
						</xsl:choose>
						<td style="width:150px;"/>
						<td>
							<xsl:variable name="documentName">
								<xsl:call-template name="findDocumentType">
									<xsl:with-param name="entryDetail" select="."/>
								</xsl:call-template>
							</xsl:variable>
							<xsl:choose>
								<xsl:when test="string-length($documentNumber) > 0 or string-length($documentDate) > 0">
									<xsl:choose>
										<xsl:when test="string-length($documentNumber) > 0 ">
											<span style="font-weight:bold;">
												<xsl:value-of select="$documentName"/> No : </span>
											<xsl:value-of select="$documentNumber"/>
											<xsl:text>&#160;</xsl:text>
										</xsl:when>
									</xsl:choose>
									<xsl:choose>
										<xsl:when test="string-length($documentDate) > 0 ">
											<span style="font-weight:bold;">
												<xsl:value-of select="$documentName"/> Tarihi : </span>
											<xsl:call-template name="convertDate">
												<xsl:with-param name="date" select="$documentDate"/>
											</xsl:call-template>
										</xsl:when>
									</xsl:choose>
								</xsl:when>
								<xsl:otherwise>
									<span style="font-weight:bold;">Belge Türü : </span>
									<xsl:value-of select="$documentName"/>
								</xsl:otherwise>
							</xsl:choose>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
		</table>
	</xsl:template>
	<!-- printEntryHeaderFooter  -->
	<xsl:template name="printEntryHeaderFooter">
		<div class="entryHeaderFooter">
			<xsl:value-of select="gl-cor:entryComment"/>
			<br/>
			<xsl:text>Muhasebe Fiş No : </xsl:text>
			<xsl:value-of select="gl-cor:entryNumber"/>
		</div>
	</xsl:template>
	<xsl:template name="convertDate">
		<xsl:param name="date"/>
		<xsl:value-of select="concat(substring($date,9,2),'/', substring($date, 6,2), '/', substring($date,1,4))"/>
	</xsl:template>
	<xsl:template name="findDocumentType">
		<xsl:param name="entryDetail"/>
		<xsl:choose>
			<xsl:when test="$entryDetail/gl-cor:documentType = 'check'">
				<xsl:text>Çek </xsl:text>
			</xsl:when>
			<xsl:when test="$entryDetail/gl-cor:documentType = 'invoice'">
				<xsl:text>Fatura</xsl:text>
			</xsl:when>
			<xsl:when test="$entryDetail/gl-cor:documentType = 'order-customer'">
				<xsl:text>Müşteri Siparişi</xsl:text>
			</xsl:when>
			<xsl:when test="$entryDetail/gl-cor:documentType = 'order-vendor'">
				<xsl:text>Satıcı Siparişi</xsl:text>
			</xsl:when>
			<xsl:when test="$entryDetail/gl-cor:documentType = 'voucher'">
				<xsl:text>Senet</xsl:text>
			</xsl:when>
			<xsl:when test="$entryDetail/gl-cor:documentType = 'shipment'">
				<xsl:text>Navlun</xsl:text>
			</xsl:when>
			<xsl:when test="$entryDetail/gl-cor:documentType = 'receipt'">
				<xsl:text>Makbuz</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$entryDetail/gl-cor:documentTypeDescription"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
