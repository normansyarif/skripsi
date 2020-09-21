function calculate_f(p, df1, df2) {

	var d1 = df1;
	var d2 = df2;  


	var prob_str  = p
	var res = 0.0

	var Fcrit = f_icdf(1.0 - Number(prob_str),d1,d2);  

	var pmesg = '';

	pmesg = Fcrit.toFixed(5);

	return pmesg;
}

function f_icdf(prob,p1,p2)
{
	var res;   
	res = inverse_beta(prob, p1/2, p2/2);
	res = (p2*res)/(p1 - (p1*res));
	
	if (prob == 0) 
		res = 0;
	
	return res;
}

function inverse_beta(p, alpha, beta)
{
	var x = 0;
	var a = 0;
	var b = 1;
	var precision = 1e-15;
	
	var iter_num = 0;
	
	while (((b - a) > precision) & (iter_num < 100))
	{
		x = (a + b) / 2;
		
		
		if (betainc_num(x,alpha,beta) > p)
			b = x;
		else
			a = x;
		iter_num = iter_num + 1;
	}
	
	return x;
}

function betainc_num(x, a, b)
{
	var bt;
	
	var g_ab;
	var g_a; 
	var g_b;
	
	if ((a + b) >= 171)
		g_ab = land_lgamma_stirling(a+b);
	else
		g_ab = Math.log(land_ios_gamma(a+b));
	
	if (a >= 171)
		g_a = land_lgamma_stirling(a);
	else
		g_a = Math.log(land_ios_gamma(a));
	
	if (b >= 171)
		g_b = land_lgamma_stirling(b);
	else
		g_b = Math.log(land_ios_gamma(b));
	
	
	bt = Math.exp(g_ab - g_a - g_b + a*Math.log(x)+b*Math.log(1.0-x));
	
	
	if (x == 0)
		bt = 0;
	
	if (x < ((a + 1.0)/(a + b + 2.0)))
		return bt*betacf(a,b,x)/a;
	else
		return 1 - (bt*betacf(b,a,1.0-x)/b);
}

function land_ios_gamma(x)
{
	var g = 7;
	
	var y;
	var t;
	var res_fr;
	
	var p = new Array() 
	
	p[0] = 0.99999999999980993;
	p[1] = 676.5203681218851;
	p[2] = -1259.1392167224028;
	p[3] = 771.32342877765313;
	p[4] = -176.61502916214059;
	p[5] = 12.507343278686905;
	p[6] = -0.13857109526572012;
	p[7] = 9.9843695780195716e-6;
	p[8] = 1.5056327351493116e-7;
	
	if (Math.abs(x - Math.floor(x)) < 1e-16)
	{
		if ( x > 1)
			return sFact(x - 1);
		else if (x == 1)
			return 1;
		else
			return 1/0.0;
	}
	else
	{
		x -= 1;
		
		y = p[0];
		
		for (i=1; i < g+2; i++)
		{
			y = y + p[i]/(x + i);
		}
		t = x + g + 0.5;
		
		
		res_fr = Math.sqrt(2*Math.PI) * Math.exp(((x+0.5)*Math.log(t))-t)*y;
		
		return res_fr;
	}
}

function sFact(num)
{
	var rval=1;
	for (var i = 2; i <= num; i++)
		rval = rval * i;
	return rval;
}

function betacf(a, b, x)
{
	var maxit = 100;
	var eps = 3e-16;
	var fpmin = 1e-30;
	var aa;
	var c;
	var d;
	var del;
	var h;
	var qab; 
	var qam;
	var qap;
	

	qab = a + b;
	qap = a + 1;
	qam = a - 1;
	
	c = 1.0;
	d = 1.0 - qab*x/qap;
	
	if (Math.abs(d)<fpmin)
		d = fpmin;
	
	d = 1.0/d;
	
	h = d;
	
	var m2;
	
	for (m = 1; m < maxit; m++)
	{
		m2 = 2*m;
		aa = m*(b-m)*x/((qam + m2)*(a + m2));
		d = 1.0 + aa*d;
		
		if (Math.abs(d)<fpmin)
			d = fpmin;
		
		c = 1.0 + aa/c;
		
		if (Math.abs(c)<fpmin)
			c = fpmin;
		
		d = 1.0/d;
		h = h*d*c;
		aa = -(a + m)*(qab + m)*x/((a+m2)*(qap+m2));
		d = 1.0 + aa*d;
		
		if (Math.abs(d)<fpmin)
			d = fpmin;
		
		c = 1.0 + aa/c;
		
		if (Math.abs(c)<fpmin)
			c = fpmin;
		
		d = 1.0/d;
		del = d*c;
		h = h*del;
		
		if (Math.abs(del-1.0)< eps)
		{
	            // std::cout << "Breaking out at iter " << m << std::endl;
	            break;
	        }
	    }
	    // std::cout << " h is " << h << std::endl;
	    return h;
	}
