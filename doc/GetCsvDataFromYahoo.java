package test;

import java.io.BufferedReader;
import java.io.File;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.SocketException;
import java.net.URL;
import java.net.URLConnection;
import java.text.DecimalFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;


public class GetCsvDataFromYahoo {
 public static final String YAHOO_FINANCE_URL = "http://table.finance.yahoo.com/table.csv?";
 public static final String YAHOO_FINANCE_URL_TODAY = "http://download.finance.yahoo.com/d/quotes.csv?";
 
 private int index;
 private Date x;//time
 private double y1 = 0.0;
 private double y2 = 0.0;
 private double y3 = 0.0;
 private double y4 = 0.0;
 private double y5 = 0.0;
 private double y6 = 0.0;
 
 public GetCsvDataFromYahoo() {
  super();
 }
 
 public GetCsvDataFromYahoo(Date x, double y1) {
  super();
  this.x = x;
  this.y1 = y1;
 }
 public GetCsvDataFromYahoo(Date x, double y1, double y2) {
  super();
  this.x = x;
  this.y1 = y1;
  this.y2 = y2;
 } 
 public GetCsvDataFromYahoo(Date x, double y1, double y2, double y3) {
  super();
  this.x = x;
  this.y1 = y1;
  this.y2 = y2;
  this.y3 = y3;
 }
 public GetCsvDataFromYahoo(Date x, double y1, double y2, double y3,double y4) {
  super();
  this.x = x;
  this.y1 = y1;
  this.y2 = y2;
  this.y3 = y3;
  this.y4 = y4;
 }
 public GetCsvDataFromYahoo(Date x, double y1, double y2, double y3,double y4, double y5) {
  super();
  this.x = x;
  this.y1 = y1;
  this.y2 = y2;
  this.y3 = y3;
  this.y4 = y4;
  this.y5 = y5;
 }
 public GetCsvDataFromYahoo(Date x, double y1, double y2, double y3,double y4, double y5, double y6) {
  super();
  this.x = x;
  this.y1 = y1;
  this.y2 = y2;
  this.y3 = y3;
  this.y4 = y4;
  this.y5 = y5;
  this.y6 = y6;
 }

 
 public void setIndex(int index) {
	 this.index = index;
 }
 public Date getX() {
  return x;
 }

 
 public void setX(Date x) {
  this.x = x;
 }

 
 public double getY1() {
  return y1;
 }

 
 public void setY1(double y1) {
  this.y1 = y1;
 }

 
 public double getY2() {
  return y2;
 }

 
 public void setY2(double y2) {
  this.y2 = y2;
 }

 
 public double getY3() {
  return y3;
 }

 
 public void setY3(double y3) {
  this.y3 = y3;
 }
 
 
 public double getY4() {
  return y4;
 }

 
 public void setY4(double y4) {
  this.y4 = y4;
 }

 
 public double getY5() {
  return y5;
 }

 
 public void setY5(double y5) {
  this.y5 = y5;
 }

 
 public double getY6() {
  return y6;
 }
 
 
 public void setY6(double y6) {
  this.y6 = y6;
 }
 
 
 public static ArrayList  getStockCsvData(String stockName, int T) throws Exception {
  ArrayList list = new ArrayList();
  
  //===================================================
  // 创建url
  //===================================================
//   参数 
//   s – 股票名称
//   a – 起始时间，月
//   b – 起始时间，日
//   c – 起始时间，年
//   d – 结束时间，月
//   e – 结束时间，日
//   f – 结束时间，年
//   g – 时间周期。Example: g=w, 表示周期是’周’。d->’日’(day),
//       w->’周’(week)，m->’月’(mouth)，v->’dividends only’

  
  String period = "";
  if (T > 0) {
   //
   Calendar cal = Calendar.getInstance(Locale.CHINESE);   
   int d = cal.get(Calendar.MONTH);
   int e = cal.get(Calendar.DAY_OF_MONTH);
   int f = cal.get(Calendar.YEAR);
   

   cal.add(Calendar.DAY_OF_MONTH , 0 - T);
   //System.out.print("获取数据\t" + stockName);
   //System.out.print("\t" + cal.getTime().toLocaleString());
   //System.out.println("\t" + cal.getTime().toLocaleString());
   
   
   
   
   int a = cal.get(Calendar.MONTH);
   int b = cal.get(Calendar.DAY_OF_MONTH);
   int c = cal.get(Calendar.YEAR);
   period = "&a=" + a + "&b=" + b + "&c=" + c
     + "&d=" + d + "&e=" + e + "&f=" + f;
  } else {
   // <=0 时，取全部数据
   period = "";
  }

  String url = YAHOO_FINANCE_URL + "s=" + stockName + period;
  
  //=======================================
  //  从URL获取数据，并且解析每一行数据
  //=========================================
  URL MyURL = null;
  URLConnection con = null;
  InputStreamReader ins = null;
  BufferedReader in = null;

  try {
   MyURL = new URL(url);
   con = MyURL.openConnection();   
   ins = new InputStreamReader(con.getInputStream(),"UTF-8");
   in = new BufferedReader(ins);

   // 标题行
   String newLine = in.readLine();
   
   // 今天的股票数据
   // TODO Yahoo财经股票数据接口，取得的历史数据不包含今天的股票数据。
   //list.add(createObject(getStockTodayData(stockName).trim()));
      
   // 历史数据，不含今天数据
   int index = 0;
   while ((newLine = in.readLine()) != null) {
    //System.out.println(newLine);
    list.add(createObject(newLine.trim(), ++index));
    if (index >= 360) {
    	break;
    }
   }

  } catch (Exception ex) {
   ex.printStackTrace();
  } finally {
   if(in != null)
    in.close();
  }
  
  return list;
 }
 
 
 
 public static String getStockTodayData(String stockName) throws Exception {
   
  String result = "";
  //===================================================
  // 创建url
  //===================================================
  //   【例子】
//   http://download.finance.yahoo.com/d/quotes.csv?s=000300.ss&f=d1ohgl1vl1
//   d1  Last Trade Date 交易日期
//   o   Open  开盘价
//   h   Day’s High 最高价
//   g   Day’s Low  最低价
//   l1  Last Trade (with Date) 最后一次交易价格，相当于收盘价
//   v   Volume  交易量
//   l1  Last Trade (with Date) 最后一次交易价格  (今天的收盘价当做加权价格)
//   
//   k   52-week High   年内最高价
//   j   52-week Low  年内最低价


  String url = YAHOO_FINANCE_URL_TODAY + "s=" + stockName + "&f=d1ohgl1vl1";

  
  //=======================================
  //  从URL获取数据
  //=========================================
  URL MyURL = null;
  URLConnection con = null;
  InputStreamReader ins = null;
  BufferedReader in = null;
  try {
   MyURL = new URL(url);
   con = MyURL.openConnection();   
   ins = new InputStreamReader(con.getInputStream(),"UTF-8");
   in = new BufferedReader(ins);
   result = in.readLine();
   
   // http response返回的字符串中，日期包含 双引号“ 必须删掉。
   if(result != null) result = result.replace("\"", "");   
  } catch (SocketException ex) {
   ex.printStackTrace();
  } catch (Exception ex) {
   ex.printStackTrace();
  } finally { 
   if(in != null)
    in.close();
  }
  return result;
 }
 
 
 
 public static GetCsvDataFromYahoo createObject(String line, int index){
  GetCsvDataFromYahoo point = new GetCsvDataFromYahoo();
  int pos = 0;
  String format = null;
  String [] data = null;
  if(line.indexOf(',') > 0){
   data = line.split(",");
  } else {
   data = line.split(" ");
  }
  try {
   // 寻找合适的日期格式
   if (data[0].indexOf('-') > 0) {
    if (data[0].length() == 10)
     format = "yyyy-MM-dd";
    else
     format = "yyyy-M-d";
   } else if (data[0].indexOf('.') > 0) {
    if (data[0].length() == 10)
     format = "yyyy.MM.dd";
    else
     format = "yyyy.M.d";
   } else if (data[0].indexOf('/') > 0) {
    if (data[0].length() == 10){
     if(data[0].indexOf('/') > 3)
      format = "yyyy/MM/dd";//年月日
     else
      format = "MM/dd/yyyy";//月日年
    } else {
     if(data[0].indexOf('/') > 3)
      format = "yyyy/M/d";//年月日
     else
      format = "M/d/yyyy";//月日年
    }
     
   }
   
   //Date,Open,High,Low,Close,Volume,Adj Close
   SimpleDateFormat sdf = new SimpleDateFormat(format);
   try {
//    point.setX(sdf.parse(data[pos++]));
	   point.setX(sdf.parse(data[pos++]));
	   point.setIndex(index);
   } catch (ParseException ex) {
    ex.printStackTrace();
   }
   point.setY1(parseDouble(data[pos++]));//Open
   point.setY2(parseDouble(data[pos++]));//High
   point.setY3(parseDouble(data[pos++]));//Low
   point.setY4(parseDouble(data[pos++]));//Close
   point.setY5(parseDouble(data[pos++]));//Volume
   point.setY6(parseDouble(data[pos++]));//Adj Close
  }catch (Exception ex){
   // 发生异常
   ex.printStackTrace();
  }finally {   
  } 
  return point;
 }
 
 
 static double  parseDouble(String data){
  double result = 0.0;
  try {
   if (data == null || data.trim().length() == 0)
    result = Double.valueOf(0.0);
   else
    result = Double.valueOf(data.trim());
  } catch (NumberFormatException ex) {
   ex.printStackTrace();
   System.out.print(data);
  }
  return result;
 }
 
 
 
 public String toString(){
  StringBuffer s = new StringBuffer();
//  s.append(format.format(x).toString());
  s.append(index);
  s.append(",");
  s.append(df2.format(y1));
  s.append(",");
  s.append(df2.format(y2));
  s.append(",");
  s.append(df2.format(y3));
  s.append(",");
  s.append(df2.format(y4));
  s.append(",");
  s.append(df0.format(y5));
  s.append(",");
  s.append(df2.format(y6));
  
  return s.toString();
  
 }
 
 
 static SimpleDateFormat format = new SimpleDateFormat("yyyy/MM/dd");
 static DecimalFormat df2 = (DecimalFormat) DecimalFormat.getInstance();
 static DecimalFormat df0 = (DecimalFormat) DecimalFormat.getInstance();
 static {
  df0.applyPattern("0");
  df2.applyPattern("0.000");
 }
 
 
 public static void main(String args[]) throws Exception{
  
  // 沪市后缀名.ss 例子： 沪深300 000300.ss ,深市后缀名 .sz 例子： 399106.sz, 600320.ss

	 String[] stocks = new String[]{
			 "TAOM", "YOKU", "SINA", "BIDU", "SOHU", "RENN","DANG","PCLN",
			 "GOOG","PCLN","AMZN","AAPL","IBM","INTC","MSFT","HPQ",
			 "600320.ss","000538.sz","600519.ss","000002.sz","600895.ss"};
	 
	 for (String stock :stocks) {
		 PrintWriter out = new PrintWriter(new File("stock_" + stock + ".csv"), "UTF-8");
		  ArrayList  list = GetCsvDataFromYahoo.getStockCsvData("TAOM",600);
		  GetCsvDataFromYahoo csvData = null;
		  out.println("key,open,highest,lowest,close,volumn,weight");
		  for(int i = 0 ;i < list.size() ; i++){
		    csvData = (GetCsvDataFromYahoo)list.get(i);
		    out.println(csvData.toString() );
		  }
		  out.close();
	 }
	 System.out.println("Done");

 }
}