var serviceUrl = "https://abc.com";
var linkedin_url = "";
var $member_id = "";
var dataArray = [];
var itemToBeAdded = null;

//functions 
function getUrlVars(URL)
{
    var vars = [], hash;
    var hashes = URL.slice(URL.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

function displayModal(content) {
    if ($("#abc-modal-bg").length > 0)
        return;

    $("body").append("<div id='abc-modal-bg'></div>");
    $("#abc-modal-bg").append("<div id='abc-modal'></div>");

    $("#abc-modal").html("\
            <h2>" + content['headline'] + "</h2>\
            <p>" + content['body'] + "</p>\
            <p><a class='add-to-abc' href='" + content['linkURL'] + "' target='_blank'>" + content['linkText'] + "</a>\
             <a id='abc-modal-close' href='#'>Close</a></p>\
  ");

    $("#abc-modal").addClass("in");

    $("#abc-modal-close").on("click", function (e) {
        $("#abc-modal").removeClass("in");
        setTimeout(function () {
            $("#abc-modal-bg").fadeOut(200, function () {
                $(this).remove();
                window.location.reload();
            });
        }, 500);
    })
}

function displayErrorAbcLoginModal() {
    displayModal({
        headline: "Oops!",
        body: "You are not logged in to abc.",
        linkURL: "http://login.abc.com/",
        linkText: "Open abc"
    });
}
function displayErrorModal(error) {
    if (error != '') {
        displayModal({
            headline: "Oops! We encountered an error!",
            body: error,
            linkURL: "https://abc.com/",
            linkText: "Open Help Page"
        });
    } else {
        displayModal({
            headline: "Oops! We encountered an error!",
            body: "There was an error adding this lead. Please check the help page or contact support@abc.com.",
            linkURL: "https://abc.com/",
            linkText: "Open Help Page"
        });
    }
}
function displayErrorCreditsModal() {
    displayModalForCredit({
        headline: "Uh oh!",
        body: "You do not have enough credits. Upgrade your plan or logged to http://abc.com and purchase credits.",
        linkURL: "https://abc.com/",
        linkText: "Get More Credits"
    });
}

linkedin_url = document.URL;

var Id = getUrlVars(linkedin_url)["id"];
if (Id != 'undefined') {
    $member_id = Id;
}

$.ajax({
    dataType: "json",
    type: 'POST',
    url: serviceUrl + '/dashboard/signin/ifsigned',
    data: "linkedin_url=" + encodeURIComponent(linkedin_url) + "&member_id=" + $member_id,
    success: function (JSON) {
        if (JSON.status == true) {
            if ($(".top-card .profile-overview .profile-aux").length == 1) {

                if (JSON.contact == true) {

                    $("#name .fn").after("<span><div class='added-lead-to-abc'><span class='view'><a href='" + serviceUrl + "/dashboard/#!show-list' target='_blank'>View</a></span></div></span>");
                } else {
                    var name = $("#name .full-name").text();
                    if (name !== "LinkedIn Member") {
                        $("#name .fn").after("<span><div id='add-lead-to-abc' class='add-lead-to-abc'>Add Lead</div><div class='abc-please-wait invisible'>Please wait...</div></span>");
                    }
                }

            }
        }
    }
});


$("#name").on("click", "#add-lead-to-abc", function (e) {


    var that = this;
    e.preventDefault();

    itemToBeAdded = $("#add-lead-to-abc");

    var linkedin_url = document.URL;
    var photo = $(".profile-picture img").attr("src");
    var fullname = $(".full-name").text();
    var $fullname = fullname.split(" ");

    var firstname = "";
    var lastname = "";

    if ($fullname.length > 1) {
        firstname = $fullname[0];
        lastname = $fullname[1];
    }
    else
        firstname = fullname;

    var title = "";
    var company_name = "";
    var company_url = "";
    var index = 0;
    var work_history = "";
    var location = "";
    var industry = "";
    var summary = "";
    var education = "";
    var previous = "";
    var $member_id = "";
    var user_public_profile = "";

    try {
        var Id = getUrlVars(linkedin_url)["id"];
        if (Id != 'undefined') {
            $member_id = Id;
        }
    } catch (e) {
    }

    try {
        $("#overview-summary-past td ol li").each(function () {
            work_history += encodeURIComponent($(this).find("strong").text()) + "separator_p";
        });
    } catch (e) {
    }

    try {
        summary = $(".summary .description").text();
    } catch (e) {
    }

    try {
        location = $("#location .locality").text();
    } catch (e) {
    }
    try {
        industry = $("#location .industry").text();
    } catch (e) {
    }


    try {
        title = $("#background-experience header h4 a").eq(0).text();
        company_name = $("#background-experience header h5 span a").eq(0).text();
    } catch (e) {
    }


    try {
        if (title === '' || company_name === '') {
            title = $(".top-card .profile-overview .title").text();

            var title2 = "";
            var title1 = title.split(" at ");
            if (title1.length == 2) {
                if (title == '') {
                    title = title1[0];
                }
                if (company_name == '') {
                    company_name = title1[1];
                }
            }
            else {
                title2 = title.split(" of ");
                if (title2.length == 2) {
                    if (title == '') {
                        title = title2[0];
                    }
                    if (company_name == '') {
                        company_name = title2[1];
                    }
                }
            }
        }
    } catch (e) {
    }

    try {
        var company_name_tmp = $("#overview-summary-current td ol li").eq(0).find("strong").text();
        if (company_name_tmp != "") {
            company_name = company_name_tmp;
        }
        company_url = $("#overview-summary-current td ol li").eq(0).find("strong a").attr('href');
        if (typeof (company_url) != 'undefined' && company_url != '') {
            company_url = 'https://www.linkedin.com' + company_url;
        } else {
            company_url = '';
        }

        //previous = $("#overview-summary-past td ol li").find("strong").text();
        var prev_arr = new Array();
        $("#overview-summary-past td ol").find("li").each(function () {
            var current = $(this);
            prev_arr.push(current.find("a").text());
        });

        previous = prev_arr;

        //Educaiton
        education = $("#overview-summary-education td ol li a").eq(0).text();


        user_public_profile = $(".public-profile").eq(0).find("a").eq(0).text();

        if (user_public_profile != '') {
            linkedin_url = user_public_profile;
        }
    } catch (e) {
    }

    $(".top-card .profile-overview #name .add-lead-to-abc").addClass("invisible");
    $(".top-card .profile-overview #name .abc-please-wait").removeClass("invisible");

    $.ajax({
        dataType: "json",
        type: 'POST',
        url: serviceUrl + '/dashboard/signin/ifsigned',
        success: function (JSON) {
            if (JSON.status == true) {

                $.ajax(serviceUrl + '/search/add-lead-to-abc', {
                    dataType: "json",
                    type: 'POST',
                    data: "&linkedin_url=" + encodeURIComponent(linkedin_url) + "&member_id=" + $member_id + "&photo=" + photo + "&fullname=" + encodeURIComponent(fullname) + "&firstname=" + encodeURIComponent(firstname) + "&lastname=" + encodeURIComponent(lastname) + "&title=" + encodeURIComponent(title) + "&company_name=" + encodeURIComponent(company_name) + "&company_url=" + encodeURIComponent(company_url) + "&work_history=" + work_history + "&location=" + location + "&education=" + encodeURIComponent(education) + "&previous=" + encodeURIComponent(previous) + "&industry=" + encodeURIComponent(industry) + "&is_scrape=1&summary=" + encodeURIComponent(summary)
                })
                        .done(function (data, textStatus, xhr) {

                            if (data.error != "") {

                                $(".top-card .profile-overview #name .add-lead-to-abc").removeClass("invisible");
                                $(".top-card .profile-overview #name .abc-please-wait").addClass("invisible");
                                displayErrorModal(data.error);
                                //console.log(data.error);
                            }
                            else
                            {
                                //for adding in my contacts tab								 
                                $.ajax({
                                    dataType: "json",
                                    type: "POST",
                                    url: serviceUrl + '/search/contact/purchase',
                                    data: "index=0",
                                    success: function (JSON) {
                                        //console.log(JSON);								
                                        if (JSON.error != '') {
                                            if (JSON.credits < 1) {
                                                //console.log('You are out of credits, logged to http://abc.com and purchase credits.');			
                                                NewdisplayModalForCredit(JSON);
                                            } else {
                                                displayErrorModal(JSON.error);
                                                //console.log(JSON.error);
                                            }
                                        } else {
                                            //success
                                            $(".top-card .profile-overview #name .abc-please-wait").remove();
                                            $(that).replaceWith("<div class='added-lead-to-abc'><span class='view'><a href='" + serviceUrl + "/dashboard/#!show-list' target='_blank'>View</a></span></div>");
                                        }
                                    }
                                });
                                //end
                            }
                        })
                        .fail(function (xhr, errorMessage, error) {

                            $(".top-card .profile-overview #name .add-lead-to-abc").removeClass("invisible");
                            $(".top-card .profile-overview #name .abc-please-wait").addClass("invisible");
                            displayErrorModal(errorMessage);

                            if (xhr.status == 401) {
                            } else {
                            }
                        });
            }
            else {
                $(".top-card .profile-overview #name .abc-please-wait").remove();
                //console.log("You can't add Lead because you are not logged to http://abc.com");
                displayErrorAbcLoginModal();
            }
        }
    });

});

// for linkedin search and advance search page.

$.ajax({
    dataType: "json",
    type: 'POST',
    url: serviceUrl + '/dashboard/signin/ifsigned',
    data: "linkedin_url=" + encodeURIComponent(linkedin_url) + "&member_id=" + $member_id,
    success: function (JSON) {

        if (JSON.status == true) {

            if ($("#results-container #results .result .bd .srp-actions .primary-action-button").length > 0) {

                $('body').append('<div id="abc-box"><a href="http://abc.com" class="abc-logo">abc</a><a href="#" id="hide-abc-box">Hide</a><div id="abc-buttons"><a class="add-to-abc add-all" href="#">Add All Leads to abc</a><a class="add-to-abc add-all-and-next" href="#">Add All and Next Page</a></div><div class="abc-sucess invisible">All leads has been added.</div><div class="abc-please-wait invisible">Please wait...</div></div>');
                $("body").prepend("<div id='abc-box-toggle'><a href='#'>Show abc</a></div>");

                $('#results > li').each(function () {
                    var that = this;
                    var added = "No";
                    if ($(this).find(".title").length > 0) {

                        var linkedin_url = $(this).closest(".result").find("a").attr("href");
                        var photo = $(this).closest(".result").find("img").attr("src");
                        var fullname = $(this).closest(".result").find(".title").text();
                        var title = $(this).closest(".result").find(".description").text();
                        var description = $(this).closest(".result").find(".description").text();
                        var member_id = $(this).closest(".result").data("li-entity-id");
                        var location_add = $.trim($(this).closest(".result").find(".demographic dd").first().text());
                        var company_name = "";

                        $.ajax({
                            dataType: "json",
                            type: 'POST',
                            url: serviceUrl + '/dashboard/signin/ifsigned',
                            data: "linkedin_url=" + encodeURIComponent(linkedin_url) + "&member_id=" + member_id,
                            success: function (JSONP) {
                                if (JSONP.status == true) {
                                    if (JSONP.contact == true) {
                                        $(that).closest(".result").find(".srp-actions").append("<br><div class='search-added-lead-button'><span class='view'><a href='" + serviceUrl + "/dashboard/#!show-list' target='_blank'>View</a></span></div>");
                                    } else {
                                        var name = $(that).closest(".result").find(".title").text();
                                        if (name !== "LinkedIn Member") {
                                            $(that).closest(".result").find(".srp-actions").append("<br><div id='search-add-lead-to-abc' class='add-lead-to-abc search-add-lead-button'>Add</div><div class='abc-please-wait invisible'>Please wait...</div>");
                                        }

                                    }
                                }
                            }
                        });

                        var $fullname = fullname.split(" ");
                        var firstname = "";
                        var lastname = "";

                        if ($fullname.length > 1) {
                            firstname = $fullname[0];
                            lastname = $fullname[1];
                        }
                        else
                            firstname = fullname;

                        var title2 = "";
                        var title1 = title.split(" at ");
                        if (title1.length == 2) {
                            title = title1[0];
                            company_name = title1[1];
                        }
                        else {
                            title2 = title.split(" of ");
                            if (title2.length == 2) {
                                title = title2[0];
                                company_name = title2[1];
                            }
                        }

                        var obj = {
                            "linkedin_url": linkedin_url,
                            "photo": photo,
                            "title": title,
                            "fullname": fullname,
                            "firstname": firstname,
                            "lastname": lastname,
                            "company_name": company_name,
                            "description": description,
                            "member_id": member_id,
                            "location_add": location_add
                        }
                        dataArray.push(obj);

                    }
                });

                //for add all and next page button
                $("#abc-box").on("click", ".add-all-and-next", function (e) {
                    console.log(dataArray);
                    $(".add-all").addClass("invisible");
                    $(".add-all-and-next").addClass("invisible");
                    $("#abc-box").find(".abc-please-wait").removeClass("invisible");

                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: serviceUrl + '/search/contact/saveall',
                        data: {'myArray': dataArray}, //stringify is important,
                        success: function (JSONP) {
                            console.log(JSONP)
                            if (JSONP.error != '') {
                                if (JSONP.credits < 1) {

                                    //displayErrorCreditsModal();
                                    NewdisplayModalForCredit(JSONP);
                                } else {

                                    displayErrorModal(JSONP.error);
                                }
                            } else {
                                $('#results-col').find(".add-lead-to-abc").replaceWith("<div class='search-added-lead-button'><span class='view'><a href='" + serviceUrl + "/dashboard/#!show-list' target='_blank'>View</a></span></div>");
                                $("#abc-box").find(".abc-please-wait").addClass("invisible");
                                $("#abc-box").find(".abc-sucess").removeClass("invisible");

                                var $pagination = $('#results-col #results-pagination').text();
                                if ($pagination != '') {
                                    var nextPage = $('#results-col #results-pagination .active').next();
                                    document.location = $(nextPage).find('a').attr('href');
                                } else {

                                    displayErrorModal('Page not found.');
                                }
                            }
                        }
                    });

                });
                // for add all button
                $("#abc-box").on("click", ".add-all", function (e) {
                    console.log(dataArray);
                    itemToBeAdded = $(".add-all");

                    $(".add-all").addClass("invisible");
                    $(".add-all-and-next").addClass("invisible");
                    $("#abc-box").find(".abc-please-wait").removeClass("invisible");

                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: serviceUrl + '/search/contact/saveall',
                        data: {'myArray': dataArray}, //stringify is important,
                        success: function (JSONP) {
                            console.log(JSONP)
                            if (JSONP.error != '') {
                                if (JSONP.credits < 1) {

                                    //displayErrorCreditsModal();
                                    NewdisplayModalForCredit(JSONP);
                                } else {
                                    //console.log(JSONP.error);
                                    displayErrorModal(JSONP.error);
                                }
                            } else {
                                $('#results-col').find(".add-lead-to-abc").replaceWith("<div class='search-added-lead-button'><span class='view'><a href='" + serviceUrl + "/dashboard/#!show-list' target='_blank'>View</a></span></div>");
                                $("#abc-box").find(".abc-please-wait").addClass("invisible");
                                $("#abc-box").find(".abc-sucess").removeClass("invisible");
                            }
                        }
                    });

                });

                $("#hide-abc-box").on("click", function (event) {
                    event.preventDefault();

                    localStorage["abc-box-hidden"] = "true";

                    $("#abc-box").fadeOut();
                    $("#abc-box-toggle").addClass("show");
                });

                $("#abc-box-toggle a").on("click", function (event) {
                    event.preventDefault();

                    localStorage["abc-box-hidden"] = "false";

                    $("#abc-box-toggle").removeClass("show");
                    $("#abc-box").fadeIn();
                });

                if (localStorage["abc-box-hidden"] == "true") {
                    setTimeout(function () {
                        $("#abc-box-toggle").addClass("show");
                    }, 1);
                    $("#abc-box").hide();
                }

            }
        }
    }
});


function LoadlistButton() {

    setTimeout(function () {

        $('#results > li').each(function () {
            var that = this;
            if ($(this).find(".title").length > 0) {

                var linkedin_url = $(this).closest(".result").find("a").attr("href");
                var photo = $(this).closest(".result").find("img").attr("src");
                var fullname = $(this).closest(".result").find(".title").text();
                var title = $(this).closest(".result").find(".description").text();
                var description = $(this).closest(".result").find(".description").text();
                var member_id = $(this).closest(".result").data("li-entity-id");
                var location_add = $.trim($(this).closest(".result").find(".demographic dd").first().text());
                var company_name = "";

                $.ajax({
                    dataType: "json",
                    type: 'POST',
                    url: serviceUrl + '/dashboard/signin/ifsigned',
                    data: "linkedin_url=" + encodeURIComponent(linkedin_url) + "&member_id=" + member_id,
                    success: function (JSON) {

                        var name = $(that).closest(".result").find(".title").text();

                        if (JSON.status == true) {
                            if (JSON.contact == true) {
                                $(that).closest(".result").find(".srp-actions").append("<br><div class='search-added-lead-button'><span class='view'><a href='" + serviceUrl + "/dashboard/#!show-list' target='_blank'>View</a></span></div>");
                            } else {
                                if (name !== "LinkedIn Member") {
                                    $(that).closest(".result").find(".srp-actions").append("<br><div id='search-add-lead-to-abc' class='add-lead-to-abc search-add-lead-button'>Add</div><div class='abc-please-wait invisible'>Please wait...</div>");
                                }
                            }
                        } else {
                            if (name !== "LinkedIn Member") {
                                $(that).closest(".result").find(".srp-actions").append("<br><div class='add-lead-to-abc search-add-lead-button'>Add</div><div class='abc-please-wait invisible'>Please wait...</div>");
                            }
                        }
                    }
                });

                var $fullname = fullname.split(" ");
                var firstname = "";
                var lastname = "";

                if ($fullname.length > 1) {
                    firstname = $fullname[0];
                    lastname = $fullname[1];
                }
                else
                    firstname = fullname;

                var title2 = "";
                var title1 = title.split(" at ");
                if (title1.length == 2) {
                    title = title1[0];
                    company_name = title1[1];
                }
                else {
                    title2 = title.split(" of ");
                    if (title2.length == 2) {
                        title = title2[0];
                        company_name = title2[1];
                    }
                }

                var obj = {
                    "linkedin_url": linkedin_url,
                    "photo": photo,
                    "title": title,
                    "fullname": fullname,
                    "firstname": firstname,
                    "lastname": lastname,
                    "company_name": company_name,
                    "description": description,
                    "member_id": member_id,
                    "location_add": location_add
                }

                dataArray.push(obj);

            }
        });

        if ($(".add-all").hasClass("invisible")) {
            $(".add-all").removeClass("invisible");
            $(".add-all-and-next").removeClass("invisible");
            $("#abc-box").find(".abc-sucess").addClass("invisible");
        }

    }, 3000);

}

//var total_images = $(".search-results .result .entity-img").length;
//var page_number = $( this ).find(".page-link").data( "li-page" );

$("#results-col #results-pagination").on("click", "li", function (e) {
    LoadlistButton();
});

$("#refine-search").on("change", 'input[type="checkbox"]', function () {
    LoadlistButton();
});


$(document).on('submit', 'form', function (e) {
    var theForm = $(this);
    var formID = theForm.attr("id");

    if (formID == 'peopleSearchForm' || formID == 'refine-search') {
        LoadlistButton();
    } else {
        //console.log(formID);
    }
});

$("#results-col").on("click", "#search-add-lead-to-abc", function (e) {

    var that = this;
    itemToBeAdded = $("#search-add-lead-to-abc");
    var eventt = e;
    var linkedin_url = $(e.target).closest(".result").find("a").attr("href");
    var photo = $(e.target).closest(".result").find("img").attr("src");
    var fullname = $(e.target).closest(".result").find(".title").text();
    var title = $(e.target).closest(".result").find(".description").text();
    var description = $(e.target).closest(".result").find(".description").text();
    var member_id = $(e.target).closest(".result").data("li-entity-id");
    var location_add = $.trim($(e.target).closest(".result").find(".demographic dd").first().text());
    var company_name = "";
    var index = 0;
    var work_history = "";
    var summary = "";


    var company_url = "";
    var industry = "";
    var summary = "";
    var education = "";
    var previous = "";





    var $fullname = fullname.split(" ");
    var firstname = "";
    var lastname = "";

    if ($fullname.length > 1) {
        firstname = $fullname[0];
        lastname = $fullname[1];
    }
    else
        firstname = fullname;

    var title2 = "";
    var title1 = title.split(" at ");
    if (title1.length == 2) {
        title = title1[0];
        company_name = title1[1];
    }
    else {
        title2 = title.split(" of ");
        if (title2.length == 2) {
            title = title2[0];
            company_name = title2[1];
        }
    }

    $(this).addClass("invisible");
    $(e.target).closest(".result").find(".abc-please-wait").removeClass("invisible");

    $.ajax({
        dataType: "json",
        type: 'POST',
        url: serviceUrl + '/dashboard/signin/ifsigned',
        success: function (JSON) {
            if (JSON.status == true) {

                $.ajax(serviceUrl + '/search/add-lead-to-abc', {
                    dataType: "json",
                    type: 'POST',
                    data: "linkedin_url=" + encodeURIComponent(linkedin_url) + "&member_id=" + member_id + "&photo=" + photo + "&fullname=" + encodeURIComponent(fullname) + "&firstname=" + encodeURIComponent(firstname) + "&lastname=" + encodeURIComponent(lastname) + "&title=" + encodeURIComponent(title) + "&company_name=" + encodeURIComponent(company_name) + "&company_url=" + encodeURIComponent(company_url) + "&work_history=" + work_history + "&location=" + location_add + "&industry=" + encodeURIComponent(industry) + "&advance_serach=1&is_scrape=0&description=" + encodeURIComponent(description) + "&summary=" + encodeURIComponent(summary) + "&education=" + encodeURIComponent(education) + "&previous=" + encodeURIComponent(previous)
                })
                        .done(function (data, textStatus, xhr) {

                            if (data.error != "") {

                                $(that).removeClass("invisible");
                                $(eventt.target).closest(".result").find(".abc-please-wait").addClass("invisible");
                                //console.log(data.error);                        
                                displayErrorModal(data.error);
                            }
                            else
                            {
                                $.ajax({
                                    dataType: "json",
                                    type: "POST",
                                    url: serviceUrl + '/search/contact/purchase',
                                    data: "index=0",
                                    success: function (JSON) {

                                        if (JSON.error != '') {
                                            if (JSON.credits < 1) {
                                                //console.log('You are out of credits, logged to http://abc.com and purchase credits.');			
                                                //displayErrorCreditsModal();
                                                NewdisplayModalForCredit(JSON);
                                            } else {
                                                //console.log(JSON.error);
                                                displayErrorModal(JSON.error);
                                            }
                                        } else {
                                            //success
                                            $(eventt.target).closest(".result").find(".abc-please-wait").remove();
                                            $(that).replaceWith("<div class='search-added-lead-button'><span class='view'><a href='" + serviceUrl + "/dashboard/#!show-list' target='_blank'>View</a></span></div>");
                                        }
                                    }
                                });
                                //end						                        
                            }
                        })
                        .fail(function (xhr, errorMessage, error) {

                            $(that).removeClass("invisible");
                            $(eventt.target).closest(".result").find(".abc-please-wait").addClass("invisible");
                            displayErrorModal(errorMessage);

                            if (xhr.status == 401) {
                            } else {
                            }
                        });

            }
            else {
                $(eventt.target).closest(".result").find(".abc-please-wait").remove();
                //console.log("You can't add Lead because you are not logged to http://abc.com");
                displayErrorAbcLoginModal();
            }
        }
    });
});

function NewdisplayModalForCredit(data) {

    $("body").append("<div id='abc-credit-modal-bg'></div>");
    $("#abc-credit-modal-bg").append("<div id='abc-credit-modal'></div>");

    $("#abc-credit-modal").html("<h2>You are out of credits!</h2>\
                                        <p>You do not have enough credits. Upgrade your plan or logged to http://abc.com and purchase credits.</p>\
                                        <div style='float:left; width:110px;'><button type='button' class='add-to-abc' id='triggerBuyOneCreditModal'>Add for $1.50</button></div>\
                                        <div style='float:left; width:160px;'><button type='button' class='add-to-abc' onclick='window.location=\"https://login.abc.com/dashboard/profile/my-account#!buy-more-credits\";'>Upgrade your account</button></div>\
                                        <div style='float:left; width:50px;'><a class='add-to-abc' id='abc-credit-modal-close' href='#'>Close</a></div>\
                                        </div>\
                                        </div>");

    $("#abc-credit-modal").addClass("in");
    $("#abc-credit-modal-close").on("click", function (e) {
        $("#abc-credit-modal").removeClass("in");
        setTimeout(function () {
            $("#abc-credit-modal-bg").fadeOut(200, function () {
                $(this).remove();
                window.location.reload();
            });
        }, 500);
    });
}



var handler = StripeCheckout.configure({
    key: 'somekey',
  
    token: function (token) {
        // You can access the token ID with token.id
        //console.log(token);
        $.ajax({
            type: "POST",
            url: serviceUrl + "/dashboard/chrome-ext/make-credit-transaction", //For own custom domain, put the full https appspot url here
            data: token,
            timeout: 200000,
            beforeSend: function (settings) {
                console.log("About to send the transaction, may take a while, but this will be async")
            },
            success: function (result)
            {
                //console.log('success');
                itemToBeAdded.trigger('click');
            },
            error: function (result) {
                console.log("Error", result);
            }
        });
    }
});

$(document).on('click', '#triggerBuyOneCreditModal', function (e) {
    //$('#triggerBuyOneCreditModal').on('click', function(e) {

    $('#abc-credit-modal').removeClass('in');
    $('#abc-credit-modal-bg').fadeOut(200, function () {
        $(this).remove();
    });

    handler.open({
        name: 'abc',
        description: '1 Lead ($1.50)',
        amount: 150,
        address: true,
        image: serviceUrl + '/resources/images/logo/new-logo.png',
        closed: function () {
            $(".add-lead-to-abc").removeClass("invisible");
            $(".abc-please-wait").addClass("invisible");
        }
    });
    e.preventDefault();
});
