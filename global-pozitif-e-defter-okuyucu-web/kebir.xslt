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
	<xsl:variable name="VKN_TCKN" select="/edefter:defter/xbrli:xbrl[1]/xbrli:context[1]/xbrli:entity[1]">
	</xsl:variable>
	<xsl:template match="/edefter:defter">
		<html>
			<head>
				<meta http-equiv="X-UA-Compatible" content="IE=7"/>
				<style type="text/css">
					body.bodyClass{
						font-family:Arial; 
						font-size:12px; 
					}
					table.tablo1{
						border-collapse:collapse; 
						empty-cells:show; 
						width:100%;
						border-width:0px;
					}
					table.tablo1 thead tr th{
						background-color:gray; 
						border:solid 1px Black; 
						color:white; 
						font-weight:bold;
						padding:3px;
					}

					table.entryHeader thead tr th{
						background-color:#e1e1e1; 
						border:solid 1px Black; 
						color:black; 
						padding:3px;
					}
					table.entryHeader {
						border-collapse:collapse; 
						empty-cells:show; 
						width:100%;
						border-width:0px;
					}
					table.entryHeader tbody tr td{
						border:solid 1px Black; 
						text-align:right;
						padding:3px;
					}
					table.entryHeader tfoot tr td{
						text-align:right;
						padding:3px;
					}

				</style>
			</head>
			<body class="bodyClass">
				<xsl:apply-templates select="xbrli:xbrl"/>
			</body>
		</html>
	</xsl:template>
	<xsl:template match="xbrli:xbrl">
		<xsl:apply-templates select="gl-cor:accountingEntries"/>
	</xsl:template>
	<xsl:template match="gl-cor:accountingEntries">
		<xsl:apply-templates select="gl-cor:entityInformation"/>
		<xsl:apply-templates select="gl-cor:documentInfo"/>
		<xsl:call-template name="baslikYaz"/>
		<xsl:apply-templates select="gl-cor:entryHeader"/>
		<xsl:call-template name="genelToplamYaz"/>
	</xsl:template>
	<xsl:template match="gl-cor:entityInformation">
		<table style="font-style:normal; width:100%; " border="0" width="100%">
			<tbody style="font-style:normal; margin:0; ">
				<xsl:variable name="UNVAN" select="gl-bus:organizationIdentifiers[gl-bus:organizationDescription = 'Kurum Unvanı' ]/gl-bus:organizationIdentifier"/>
				<xsl:variable name="AD_SOYAD" select="gl-bus:organizationIdentifiers[gl-bus:organizationDescription = 'Adı Soyadı' ]/gl-bus:organizationIdentifier"/>
				<xsl:variable name="SUBE_ADI" select="gl-bus:organizationIdentifiers[gl-bus:organizationDescription = 'Şube Adı' ]/gl-bus:organizationIdentifier"/>
				<tr style="font-style:normal; height:0.48in; ">
					<td style="text-align:left; width:9.23in; ">
						<span style="font-weight:bold; ">
							<xsl:if test="$UNVAN != '' ">
								<xsl:text>Kurum Unvanı : </xsl:text>
								<xsl:value-of select="gl-bus:organizationIdentifiers[gl-bus:organizationDescription = 'Kurum Unvanı' ]/gl-bus:organizationIdentifier"/>
							</xsl:if>
							<xsl:if test="$AD_SOYAD != '' ">
								<xsl:text>Adı Soyadı : </xsl:text>
								<xsl:value-of select="gl-bus:organizationIdentifiers[gl-bus:organizationDescription = 'Adı Soyadı' ]/gl-bus:organizationIdentifier"/>
							</xsl:if>
						</span>
					</td>
					<td style="text-align:right; width:9.23in; ">
						<span style="font-size:16px; font-weight:bold; ">
							<xsl:text>BÜYÜK DEFTER</xsl:text>
						</span>
					</td>
				</tr>
				<xsl:if test="$SUBE_ADI != '' ">
					<tr style="font-style:normal; height:0.48in; ">
						<td style="text-align:left; width:9.23in; ">
							<span style="font-weight:bold; ">
								<xsl:text>Şube Adı : </xsl:text>
								<xsl:value-of select="gl-bus:organizationIdentifiers[gl-bus:organizationDescription = 'Şube Adı' ]/gl-bus:organizationIdentifier"/>
							</span>
						</td>
					</tr>
				</xsl:if>
				<tr style="font-style:normal; height:0.48in; ">
					<td style="text-align:left; width:9.23in; ">
						<span style="font-weight:bold; ">
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
		<br/>
	</xsl:template>
	<xsl:template match="gl-cor:documentInfo">
		<table style="width:100%; " border="0">
			<tbody>
				<tr>
					<td style="text-align:center; width:auto; ">
						<xsl:value-of select="gl-cor:entriesComment"/>
					</td>
				</tr>
			</tbody>
		</table>
	</xsl:template>
	<xsl:template name="baslikYaz">
		<table class="tablo1">
			<thead>
				<tr>
					<th align="center" width="79">
						<xsl:text>Yev. Tarih</xsl:text>
					</th>
					<th align="center" width="50">
						<xsl:text>Yev. No.</xsl:text>
					</th>
					<th width="60">
						<xsl:text>Hesap Kodu</xsl:text>
					</th>
					<th width="150">
						<xsl:text>Hesap Adı</xsl:text>
					</th>
					<th width="60">
						<xsl:text>M. Fiş No.</xsl:text>
					</th>
					<th>
						<xsl:text>Açıklama</xsl:text>
					</th>
					<th width="70">
						<xsl:text>Borç</xsl:text>
					</th>
					<th width="70">
						<xsl:text>Alacak</xsl:text>
					</th>
					<th colspan="2" width="146">
						<xsl:text>Bakiye</xsl:text>
					</th>
				</tr>
				<tr>
					<th align="center" width="79"/>
					<th align="center" width="50"/>
					<th width="60"/>
					<th width="150"/>
					<th width="60"/>
					<th/>
					<th width="70"/>
					<th width="70"/>
					<th width="73">
						<xsl:text>Borç</xsl:text>
					</th>
					<th width="73">
						<xsl:text>Alacak</xsl:text>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="9" style="padding:0px; " align="center" width="79"/>
				</tr>
			</tbody>
		</table>
	</xsl:template>
	<xsl:template match="gl-cor:entryHeader">
		<table class="entryHeader">
			<xsl:call-template name="entryHeaderBaslikYaz">
				<xsl:with-param name="mainId">
					<xsl:value-of select="gl-cor:entryDetail[1]/gl-cor:account[1]/gl-cor:accountMainID[1]"/>
				</xsl:with-param>
				<xsl:with-param name="mainIdDesc">
					<xsl:value-of select="gl-cor:entryDetail[1]/gl-cor:account[1]/gl-cor:accountMainDescription[1]"/>
				</xsl:with-param>
			</xsl:call-template>
			<tbody>
				<xsl:call-template name="satirlariYaz">
					<xsl:with-param name="kumulatifDebit">0</xsl:with-param>
					<xsl:with-param name="kumulatifCredit">0</xsl:with-param>
					<xsl:with-param name="entryDetail" select="gl-cor:entryDetail[1]"/>
				</xsl:call-template>
			</tbody>
			<xsl:call-template name="entryHeaderSonyaz">
				<xsl:with-param name="totalDebit">
					<xsl:value-of select="gl-bus:totalDebit"/>
				</xsl:with-param>
				<xsl:with-param name="totalCredit">
					<xsl:value-of select="gl-bus:totalCredit"/>
				</xsl:with-param>
			</xsl:call-template>
		</table>
	</xsl:template>
	<xsl:template name="entryHeaderBaslikYaz">
		<xsl:param name="mainId"/>
		<xsl:param name="mainIdDesc"/>
		<thead>
			<tr>
				<th align="center" width="79">
					<xsl:text>&#160;</xsl:text>
				</th>
				<th align="right" width="50">
					<xsl:text>&#160;</xsl:text>
				</th>
				<th width="60">
					<xsl:value-of select="$mainId"/>
				</th>
				<th width="150">
					<xsl:value-of select="$mainIdDesc"/>
				</th>
				<th width="60">
					<xsl:text>&#160;</xsl:text>
				</th>
				<th>
					<xsl:text>&#160;</xsl:text>
				</th>
				<th width="70">
					<xsl:text>&#160;</xsl:text>
				</th>
				<th width="70">
					<xsl:text>&#160;</xsl:text>
				</th>
				<th style="font-weight:bold; " width="73">
					<xsl:text>&#160;</xsl:text>
				</th>
				<th style="font-weight:bold; " width="73">
				</th>
			</tr>
		</thead>
	</xsl:template>
	<xsl:template name="satirlariYaz">
		<xsl:param name="kumulatifDebit"/>
		<xsl:param name="kumulatifCredit"/>
		<xsl:param name="entryDetail"/>
		<xsl:variable name="debit">
			<xsl:choose>
				<xsl:when test="$entryDetail/gl-cor:debitCreditCode = 'D' or $entryDetail/gl-cor:debitCreditCode = 'debit'">
					<xsl:value-of select="normalize-space($entryDetail/gl-cor:amount)"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>0</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="credit">
			<xsl:choose>
				<xsl:when test="$entryDetail/gl-cor:debitCreditCode = 'C' or $entryDetail/gl-cor:debitCreditCode = 'credit'">
					<xsl:value-of select="normalize-space($entryDetail/gl-cor:amount)"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>0</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<tr>
			<td style="text-align:center;">
				<xsl:call-template name="convertDate">
					<xsl:with-param name="postingDate" select="$entryDetail/gl-cor:postingDate"/>
				</xsl:call-template>
			</td>
			<td>
				<xsl:value-of select="$entryDetail/gl-cor:lineNumberCounter"/>
			</td>
			<td style="text-align:left;">
				<xsl:value-of select="$entryDetail/gl-cor:account[1]/gl-cor:accountSub[1]/gl-cor:accountSubID[1]"/>
			</td>
			<td style="text-align:left;">
				<xsl:value-of select="$entryDetail/gl-cor:account[1]/gl-cor:accountSub[1]/gl-cor:accountSubDescription[1]"/>
			</td>
			<td style="text-align:left;">
				<xsl:value-of select="$entryDetail/gl-cor:documentReference"/>
			</td>
			<td style="text-align:left;">
				<xsl:value-of select="$entryDetail/gl-cor:detailComment"/>
			</td>
			<td>
				<xsl:text>&#160;</xsl:text>
				<xsl:value-of select="format-number(number($debit), '###.##0,00', 'tryFormat')"/>
			</td>
			<td>
				<xsl:text>&#160;</xsl:text>
				<xsl:value-of select="format-number(number($credit), '###.##0,00', 'tryFormat')"/>
			</td>
			<td style="font-weight:bold;">
				<xsl:text>&#160;</xsl:text>
				<xsl:value-of select="format-number(number($debit+$kumulatifDebit), '###.##0,00', 'tryFormat')"/>
			</td>
			<td style="font-weight:bold;">
				<xsl:text>&#160;</xsl:text>
				<xsl:value-of select="format-number(number($credit+$kumulatifCredit), '###.##0,00', 'tryFormat')"/>
			</td>
		</tr>
		<xsl:variable name="nextNode" select="$entryDetail/following-sibling::node()[local-name()=local-name($entryDetail)][1]">

		</xsl:variable>
		<xsl:choose>
			<xsl:when test="$nextNode">
				<xsl:call-template name="satirlariYaz">
					<xsl:with-param name="kumulatifDebit">
						<xsl:value-of select="$debit + $kumulatifDebit"/>
					</xsl:with-param>
					<xsl:with-param name="kumulatifCredit">
						<xsl:value-of select="$credit + $kumulatifCredit"/>
					</xsl:with-param>
					<xsl:with-param name="entryDetail" select="$nextNode"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="entryHeaderSonyaz">
		<xsl:param name="totalDebit"/>
		<xsl:param name="totalCredit"/>
		<xsl:variable name="headerDebit">
			<xsl:value-of select="sum(gl-cor:entryDetail[gl-cor:debitCreditCode = 'D' or gl-cor:debitCreditCode = 'debit']/gl-cor:amount)"/>
		</xsl:variable>
		<xsl:variable name="headerCredit">
			<xsl:value-of select="sum(gl-cor:entryDetail[gl-cor:debitCreditCode = 'C' or gl-cor:debitCreditCode = 'credit']/gl-cor:amount)"/>
		</xsl:variable>
		<tfoot>
			<tr>
				<td colspan="5"/>
				<td style="font-weight:bold; ">
					<xsl:text>TOPLAM : </xsl:text>
				</td>
				<td>
					<xsl:value-of select="format-number(number($headerDebit), '###.##0,00', 'tryFormat')"/>
				</td>
				<td>
					<xsl:value-of select="format-number(number($headerCredit), '###.##0,00', 'tryFormat')"/>
				</td>
				<td style="font-weight:bold;">
					<xsl:value-of select="format-number(number($totalDebit), '###.##0,00', 'tryFormat')"/>
				</td>
				<td style="font-weight:bold;">
					<xsl:value-of select="format-number(number($totalCredit), '###.##0,00', 'tryFormat')"/>
				</td>
			</tr>
		</tfoot>
	</xsl:template>
	<xsl:template name="genelToplamYaz">
		<xsl:variable name="toplamBorc" select="sum(gl-cor:entryHeader/gl-cor:entryDetail[gl-cor:debitCreditCode = 'D' or gl-cor:debitCreditCode = 'debit']/gl-cor:amount)"/>
		<xsl:variable name="toplamAlacak" select="sum(gl-cor:entryHeader/gl-cor:entryDetail[gl-cor:debitCreditCode = 'C' or gl-cor:debitCreditCode = 'credit']/gl-cor:amount)"/>
		<xsl:variable name="borcFark">
			<xsl:choose>
				<xsl:when test="$toplamBorc > $toplamAlacak">
					<xsl:value-of select="number($toplamBorc - $toplamAlacak)"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="number(0)"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="alacakFark">
			<xsl:choose>
				<xsl:when test="$toplamAlacak > $toplamBorc">
					<xsl:value-of select="number($toplamAlacak - $toplamBorc)"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="number(0)"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<table style="border-collapse:collapse; empty-cells:show; width:100%; " border="0" cellpadding="3" cellspacing="0">
			<tbody>
				<tr>
					<td style="border:solid 1px Black; border-right-color:white; padding:0px; " align="center" width="86"/>
					<td style="border:solid 1px Black; border-right-color:white; padding:0px; " align="right" width="56"/>
					<td style="border:solid 1px Black; border-right-color:white; padding:0px; " width="66"/>
					<td style="border:solid 1px Black; border-right-color:white; padding:0px; " width="156"/>
					<td style="border:solid 1px Black; border-right-color:white; padding:0px; " width="66"/>
					<td style="border:solid 1px Black; padding:0px; text-align:right; ">
						<xsl:text>GENEL TOPLAM :&#160; </xsl:text>
					</td>
					<td style="border:solid 1px Black; padding:3px; text-align:right; " width="70">
						<xsl:value-of select="format-number($toplamBorc, '###.##0,00', 'tryFormat')"/>
					</td>
					<td style="border:solid 1px Black; padding:3px; text-align:right; " width="70">
						<xsl:value-of select="format-number($toplamAlacak, '###.##0,00', 'tryFormat')"/>
					</td>
					<td style="border:solid 1px Black; padding:3px; text-align:right; " width="73">
						<xsl:value-of select="format-number($borcFark, '###.##0,00', 'tryFormat')"/>
					</td>
					<td style="border:solid 1px Black; padding:3px; text-align:right; " width="73">
						<xsl:value-of select="format-number($alacakFark, '###.##0,00', 'tryFormat')"/>
					</td>
				</tr>
			</tbody>
		</table>
	</xsl:template>
	<xsl:template name="convertDate">
		<xsl:param name="postingDate"/>
		<xsl:value-of select="concat(substring($postingDate,9,2),'/', substring($postingDate, 6,2), '/', substring($postingDate,1,4))"/>
	</xsl:template>
</xsl:stylesheet>
